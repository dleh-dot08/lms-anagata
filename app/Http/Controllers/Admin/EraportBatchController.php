<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Models\EraportBatch;
use App\Models\EraportEntry;
use App\Models\EraportTemplate;
use App\Models\Eraport;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode; 
use ZipArchive;

class EraportBatchController extends Controller
{
    /**
     * List batch
     */
    public function index(Request $request)
    {
        $q = EraportBatch::query()
            ->with(['course', 'template'])
            ->orderByDesc('id');

        if ($request->filled('course_id')) {
            $q->where('course_id', $request->course_id);
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $batches = $q->paginate(20);

        return view('admin.eraport.batches.index', compact('batches'));
    }

    /**
     * Form create batch
     */
    public function create()
    {
        $courses = Course::query()->orderBy('nama_kelas', 'asc')->get();
        $templates = EraportTemplate::query()->orderByDesc('id')->get();

        $semesters = Semester::query()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        return view('admin.eraport.batches.create', compact('courses', 'templates', 'semesters'));
    }

    /**
     * Store batch (draft)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => 'required|integer',
            'template_id' => 'required|integer',
            'semester_id' => 'required|integer', // ⬅️ jadikan wajib
            'notes'       => 'nullable|string|max:1000',
        ]);

        $semester = Semester::query()->findOrFail($data['semester_id']);
        $semesterLabel = trim($semester->name . ' ' . $semester->year); // "Ganjil 2024/2025"

        $batch = EraportBatch::create([
            'course_id'      => $data['course_id'],
            'template_id'    => $data['template_id'],
            'semester_id'    => $semester->id,
            'semester_label' => $semesterLabel, // ⬅️ FIX ERROR
            'notes'          => $data['notes'] ?? null,
            'status'         => 'DRAFT',
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch dibuat.');
    }

    /**
     * Show batch detail + summary
     */
    public function show(EraportBatch $batch)
    {
        $batch->load(['course', 'template']);

        // 1) entries (WAJIB ada karena dipakai di compact)
        $entries = EraportEntry::query()
            ->where('batch_id', $batch->id)
            ->orderBy('user_id')
            ->paginate(25);

        // 2) summary validasi (punyamu)
        $summary = $this->buildValidationSummary($batch);

        // 3) map rapor per user (buat tombol PDF per baris)
        // key: user_id => ['id'=>..., 'pdf_path'=>..., 'status'=>...]
        $eraportMap = Eraport::query()
            ->where('batch_id', $batch->id)
            ->select('id', 'user_id', 'pdf_path', 'status')
            ->get()
            ->keyBy('user_id');

        return view('admin.eraport.batches.show', compact('batch', 'entries', 'summary', 'eraportMap'));
    }


    /**
     * VALIDATE:
     * - sync entries dari enrollments + scores + attendances
     * - hitung missing score / missing attendance
     * - set status validated bila tidak ada masalah (opsional)
     */
    public function validateBatch(EraportBatch $batch)
    {
        $this->ensureNotPublished($batch);

        DB::transaction(function () use ($batch) {

            // optional: tandai sedang proses
            $batch->update([
                'status' => 'VALIDATING',
            ]);

            // sync entries dari enrollments/scores/attendances
            $this->syncEntriesFromSource($batch->id, $batch->course_id);

            // cek kekurangan
            $missing = $this->buildMissingMap($batch);

            $batch->update([
                'status' => empty($missing) ? 'READY' : 'DRAFT',
                'validated_at' => now(),
            ]);
        });

        $missing = $this->buildMissingMap($batch);

        return back()
            ->with('success', 'Validasi & sinkronisasi selesai.')
            ->with('validate_result', [
                'batch_status'  => $batch->fresh()->status,
                'missing_count' => count($missing),
                'missing'       => $missing,
            ]);
    }


    private function buildMissingMap(\App\Models\EraportBatch $batch): array
    {
        $courseId = $batch->course_id;

        // total pertemuan course
        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        // peserta aktif pada course dari enrollments
        $userIds = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->whereRaw("LOWER(status) = 'aktif'") // aman untuk 'Aktif'/'aktif'
            ->pluck('user_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        if (empty($userIds)) return [];

        // siapa yg punya skor minimal 1 (score join meeting => course)
        $hasScoreIds = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->where('m.course_id', $courseId)
            ->whereNull('s.deleted_at')
            ->groupBy('s.peserta_id')
            ->pluck('s.peserta_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $hasScoreSet = array_flip($hasScoreIds);

        // jumlah meeting absensi yang tercatat per user
        $attCount = DB::table('attendances')
            ->where('course_id', $courseId)
            ->whereNotNull('meeting_id')
            ->whereNull('deleted_at')
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(DISTINCT meeting_id) as c')
            ->get()
            ->keyBy('user_id');

        // format: { user_id: [issue1, issue2, ...] }
        $missing = [];

        foreach ($userIds as $uid) {
            $issues = [];

            // nilai belum ada
            if (!isset($hasScoreSet[$uid])) {
                $issues[] = 'Nilai belum ada (scores)';
            }

            // absensi belum lengkap
            $recorded = (int)($attCount->get($uid)->c ?? 0);
            if ($totalMeetings > 0 && $recorded < $totalMeetings) {
                $issues[] = "Absensi belum lengkap ({$recorded}/{$totalMeetings})";
            }

            if (!empty($issues)) {
                $missing[(string)$uid] = $issues;
            }
        }

        return $missing;
    }

    /**
     * PUBLISH:
     * - pastikan batch sudah clear (opsional strict)
     * - lock entries
     * - generate Eraport (snapshot_json, verify_token, report_number, pdf_path)
     */
    public function publish(EraportBatch $batch)
    {
        $this->ensureNotPublished($batch);

        DB::transaction(function () use ($batch) {
            // 1) sync lagi untuk memastikan data paling baru
            $this->syncEntriesFromSource($batch->id, $batch->course_id);

            // 2) strict gate: publish hanya jika clear
            $summary = $this->buildValidationSummary($batch);
            if ($summary['missing_scores_count'] > 0 || $summary['missing_attendance_count'] > 0) {
                abort(422, 'Masih ada data kosong. Selesaikan dulu sebelum publish.');
            }

            // 3) lock entries
            EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->update([
                    'locked_at' => now(),
                    'locked_by' => Auth::id(),
                    'updated_at' => now(),
                ]);

            // 4) buat/refresh rapor final
            $entries = EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->get();

            foreach ($entries as $entry) {

                // nomor rapor + token
                $reportNumber = $this->makeReportNumber($batch, $entry);
                $token = Str::random(32);

                // ✅ Buat record rapor dulu (biar $eraport tersedia untuk payload)
                $eraport = Eraport::create([
                    'batch_id'      => $batch->id,
                    'user_id'       => $entry->user_id,
                    'report_number' => $reportNumber,
                    'verify_token'  => $token,
                    'snapshot_json' => json_encode([], JSON_UNESCAPED_UNICODE), // sementara
                    'status'        => 'PUBLISHED', // ✅ kapital
                    'published_at'  => now(),
                    'published_by'  => Auth::id(), // kalau kolom ini ada
                ]);

                // ✅ Build payload pakai $eraport yang sudah ada
                $payload = $this->buildSnapshotPayload($batch, $entry, $eraport);

                // simpan snapshot_json final
                $eraport->update([
                    'snapshot_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                ]);

                // generate pdf
                $pdfPath = $this->generatePdfFromTemplate($batch, $eraport, $payload, $entry);
                if ($pdfPath) {
                    $eraport->update(['pdf_path' => $pdfPath]);
                }
            }

            // 5) update batch status
            $batch->update([
                'status' => 'PUBLISHED',
                'published_at' => now(),
            ]);
        });

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch berhasil diterbitkan (publish).');
    }

    /**
     * REOPEN:
     * - unlock entries
     * - status balik draft
     * - (opsional) tandai rapor lama obsolete / biarkan sebagai histori
     */
    public function reopen(EraportBatch $batch)
    {
        if ($batch->status !== 'PUBLISHED') {
            return back()->with('error', 'Batch belum publish.');
        }

        DB::transaction(function () use ($batch) {
            // unlock
            EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->update([
                    'locked_at' => null,
                    'locked_by' => null,
                    'updated_at' => now(),
                ]);

            // kamu bisa memilih:
            // A) hapus rapor publish lama (supaya publish berikutnya clean)
            // B) simpan sebagai histori
            // Di bawah ini saya pilih A agar sederhana:
            Eraport::query()->where('batch_id', $batch->id)->delete();

            $batch->update([
                'status' => 'DRAFT',
                'reopened_at' => now(),
                'published_at' => null,
            ]);
        });

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch dibuka kembali (reopen). Silakan revisi nilai/absensi dari menu sumber.');
    }

    /**
     * Export ZIP semua PDF rapor dalam batch
     */
    public function exportZip(EraportBatch $batch)
    {
        if ($batch->status !== 'PUBLISHED') {
            return back()->with('error', 'Batch harus publish dulu untuk export.');
        }

        $eraports = Eraport::query()
            ->where('batch_id', $batch->id)
            ->whereNotNull('pdf_path')
            ->get();

        if ($eraports->isEmpty()) {
            return back()->with('error', 'Belum ada PDF untuk diexport.');
        }

        $zipName = 'eraport_batch_' . $batch->id . '.zip';
        $tmpPath = storage_path('app/tmp/' . $zipName);

        if (!is_dir(dirname($tmpPath))) {
            mkdir(dirname($tmpPath), 0775, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat ZIP.');
        }

        foreach ($eraports as $r) {
            $diskPath = Storage::disk('public')->path($r->pdf_path);
            if (is_file($diskPath)) {
                $fileName = Str::slug($r->report_number ?: ('eraport-' . $r->id)) . '.pdf';
                $zip->addFile($diskPath, $fileName);
            }
        }

        $zip->close();

        return response()->download($tmpPath)->deleteFileAfterSend(true);
    }

    /* ============================================================
       CORE: Sync Entries from source tables
    ============================================================ */

    private function syncEntriesFromSource(int $batchId, int $courseId): void
    {
        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        // peserta dari enrollments
        $participants = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('status', 'aktif') // adjust jika perlu
            ->select('user_id', 'mentor_id')
            ->get()
            ->keyBy('user_id');

        $userIds = $participants->keys()->map(fn($v) => (int)$v)->values();
        if ($userIds->isEmpty()) return;

        // score agg (join meetings)
        $scoreAgg = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->where('m.course_id', $courseId)
            ->whereNull('s.deleted_at')
            ->whereIn('s.peserta_id', $userIds)
            ->groupBy('s.peserta_id')
            ->selectRaw('
                s.peserta_id as user_id,
                ROUND(AVG(s.total_score),0) as avg_project_score,
                ROUND(AVG(s.program_score),0) as logic_score,
                ROUND(AVG(s.creativity_score),0) as creativity_score
            ')
            ->get()
            ->keyBy('user_id');

        // attendance agg (count distinct meeting_id)
        $attAgg = DB::table('attendances as a')
            ->where('a.course_id', $courseId)
            ->whereNotNull('a.meeting_id')
            ->whereNull('a.deleted_at')
            ->whereIn('a.user_id', $userIds)
            ->groupBy('a.user_id')
            ->selectRaw("
                a.user_id,
                SUM(CASE WHEN LOWER(a.status)='hadir' THEN 1 ELSE 0 END) as hadir_count,
                SUM(CASE WHEN LOWER(a.status)='sakit' THEN 1 ELSE 0 END) as sakit_count,
                SUM(CASE WHEN LOWER(a.status)='izin'  THEN 1 ELSE 0 END) as izin_count,
                SUM(CASE WHEN LOWER(a.status)='alpha' THEN 1 ELSE 0 END) as alpha_raw,
                COUNT(DISTINCT a.meeting_id) as meeting_recorded
            ")
            ->get()
            ->keyBy('user_id');

        $now = now();

        $rows = $userIds->map(function ($uid) use ($batchId, $participants, $scoreAgg, $attAgg, $totalMeetings, $now) {
            $enr = $participants->get($uid);
            $s = $scoreAgg->get($uid);
            $a = $attAgg->get($uid);

            $meetingRecorded = (int)($a->meeting_recorded ?? 0);
            $missing = max(0, $totalMeetings - $meetingRecorded);

            $logic = $s?->logic_score;
            $crea  = $s?->creativity_score;

            return [
                'batch_id' => $batchId,
                'user_id'  => (int)$uid,

                // mentor_id dari enrollment (opsional)
                'mentor_id' => $enr->mentor_id ?? null,

                // nilai (readonly)
                'avg_project_score' => $s?->avg_project_score,
                'logic_score' => $logic,
                'logic_predicate' => $this->predicate($logic),
                'creativity_score' => $crea,
                'creativity_predicate' => $this->predicate($crea),

                // absensi (readonly)
                'hadir_count' => (int)($a->hadir_count ?? 0),
                'sakit_count' => (int)($a->sakit_count ?? 0),
                'izin_count'  => (int)($a->izin_count  ?? 0),
                'alpha_count' => (int)($a->alpha_raw ?? 0) + $missing,

                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        // upsert (mentor_note aman tidak tertimpa)
        DB::table('eraport_entries')->upsert(
            $rows,
            ['batch_id', 'user_id'],
            [
                'mentor_id',
                'avg_project_score','logic_score','logic_predicate',
                'creativity_score','creativity_predicate',
                'hadir_count','sakit_count','izin_count','alpha_count',
                'updated_at'
            ]
        );
    }

    private function predicate(?float $score): ?string
    {
        if ($score === null) return null;
        if ($score >= 95) return 'Excellent';
        if ($score >= 90) return 'Very Good';
        if ($score >= 80) return 'Good';
        return 'Average';
    }

    /* ============================================================
       Validation Summary (missing score / missing attendance)
    ============================================================ */

    private function buildValidationSummary(EraportBatch $batch): array
    {
        $courseId = $batch->course_id;

        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        $participants = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->where('status', 'aktif')
            ->pluck('user_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        // peserta yg punya skor
        $hasScore = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->where('m.course_id', $courseId)
            ->whereNull('s.deleted_at')
            ->groupBy('s.peserta_id')
            ->pluck('s.peserta_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $missingScores = array_values(array_diff($participants, $hasScore));

        // attendance recorded count per user
        $attCount = DB::table('attendances')
            ->where('course_id', $courseId)
            ->whereNotNull('meeting_id')
            ->whereNull('deleted_at')
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(DISTINCT meeting_id) as c')
            ->get()
            ->keyBy('user_id');

        $missingAttendance = [];
        foreach ($participants as $uid) {
            $c = (int)($attCount->get($uid)->c ?? 0);
            if ($c < $totalMeetings) {
                $missingAttendance[] = [
                    'user_id' => $uid,
                    'recorded' => $c,
                    'total_meetings' => $totalMeetings,
                ];
            }
        }

        return [
            'total_participants' => count($participants),
            'total_meetings' => $totalMeetings,

            'missing_scores_count' => count($missingScores),
            'missing_scores_user_ids' => $missingScores,

            'missing_attendance_count' => count($missingAttendance),
            'missing_attendance_detail' => $missingAttendance,
        ];
    }


    private function makeReportNumber(EraportBatch $batch, EraportEntry $entry): string
    {
        // contoh: ER-<course>-<batch>-<user>
        return 'ER-' . $batch->course_id . '-' . $batch->id . '-' . $entry->user_id;
    }

    /**
     * OPTIONAL PDF generator
     * - Jika kamu pakai barryvdh/laravel-dompdf, kamu bisa render view pdf.
     * - Kalau belum, return null (pdf_path kosong) dan nanti kamu pasang generator belakangan.
     */
    private function generatePdfIfPossible(EraportBatch $batch, Eraport $eraport, array $payload): ?string
    {
        try {
            // render pdf dari blade
            $pdf = Pdf::loadView('eraport.pdf.default', [
                'batch'   => $batch,
                'eraport' => $eraport,
                'data'    => $payload,
            ])->setPaper('a4', 'portrait');

            // simpan ke public disk
            $dir = "eraport/batch_{$batch->id}";
            $filename = "eraport_{$eraport->report_number}_user{$eraport->user_id}.pdf";
            $path = "{$dir}/{$filename}"; // ini yang disimpan ke kolom pdf_path

            Storage::disk('public')->put($path, $pdf->output());

            return $path;
        } catch (\Throwable $e) {
            \Log::error('PDF gen failed: '.$e->getMessage(), ['eraport_id'=>$eraport->id]);
            return null;
        }
    }

    /* ============================================================
       Guards
    ============================================================ */

    private function ensureNotPublished(EraportBatch $batch): void
    {
        if ($batch->status === 'PUBLISHED') {
            abort(422, 'Batch sudah publish. Reopen dulu jika ingin revisi.');
        }
    }

    private function toDataUriFromStorage(string $disk, string $path): string
    {
        $bytes = Storage::disk($disk)->get($path);
        $mime  = Storage::disk($disk)->mimeType($path) ?: 'image/png';
        $b64   = base64_encode($bytes);

        return "data:{$mime};base64,{$b64}";
    }

    public function downloadEntryPdf(EraportBatch $batch, EraportEntry $entry)
    {
        if ((int)$entry->batch_id !== (int)$batch->id) abort(404);

        // Cari rapor
        $eraport = Eraport::query()
            ->where('batch_id', $batch->id)
            ->where('user_id', $entry->user_id)
            ->latest('id')
            ->first();

        // Kalau belum ada record rapor → buat dulu (token & number harus ada)
        if (!$eraport) {
            $eraport = Eraport::create([
                'batch_id'      => $batch->id,
                'user_id'       => $entry->user_id,
                'report_number' => $this->makeReportNumber($batch, $entry),
                'verify_token'  => Str::random(32),
                'snapshot_json' => json_encode([], JSON_UNESCAPED_UNICODE),
                'status'        => 'PUBLISHED',
                'published_at'  => now(),
                'published_by'  => Auth::id(),
            ]);
        }

        // Kalau PDF sudah ada & file-nya ada, langsung download
        if ($eraport->pdf_path && Storage::disk('public')->exists($eraport->pdf_path)) {
            return Storage::disk('public')->download(
                $eraport->pdf_path,
                Str::slug($eraport->report_number ?: 'eraport-'.$eraport->id).'.pdf'
            );
        }

        // Ambil payload dari snapshot_json
        $payload = json_decode($eraport->snapshot_json ?? '', true);
        if (!is_array($payload)) $payload = [];

        // ✅ Pastikan payload sesuai field_map (kalau snapshot lama, rebuild)
        if ($this->payloadNeedsUpgrade($payload)) {
            $payload = $this->buildSnapshotPayload($batch, $entry, $eraport);

            // simpan snapshot terbaru biar konsisten ke depannya
            $eraport->update([
                'snapshot_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        } else {
            // tetap paksa inject verify_url & number agar QR pasti ada
            $payload = $this->injectEraportMetaToPayload($payload, $batch, $entry, $eraport);
            $eraport->update([
                'snapshot_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            ]);
        }

        $pdfPath = $this->generatePdfFromTemplate($batch, $eraport, $payload, $entry);

        if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
            Log::error('PDF gagal dibuat (on-demand)', [
                'batch_id' => $batch->id,
                'user_id'  => $entry->user_id,
                'eraport_id' => $eraport->id,
            ]);
            return back()->with('error', 'PDF gagal dibuat. Cek template PNG + field_map + view render_png + QR generator');
        }

        $eraport->update(['pdf_path' => $pdfPath]);

        return Storage::disk('public')->download(
            $pdfPath,
            Str::slug($eraport->report_number ?: 'eraport-'.$eraport->id).'.pdf'
        );
    }

    private function payloadNeedsUpgrade(array $payload): bool
    {
        // field_map kamu butuh struktur ini:
        // student.name, semester.label, school.name, course.title,
        // attendance.summary.*, scores.avg_project, scores.logic_ct, scores.creativity,
        // mentor_note.note, eraport.number, eraport.verify_url
        return
            data_get($payload, 'student.name') === null ||
            data_get($payload, 'attendance.summary.hadir') === null ||
            data_get($payload, 'scores.avg_project') === null ||
            data_get($payload, 'eraport.verify_url') === null;
    }

    private function injectEraportMetaToPayload(array $payload, EraportBatch $batch, ?EraportEntry $entry, Eraport $eraport): array
    {
        // number wajib
        data_set(
            $payload,
            'eraport.number',
            $eraport->report_number ?: ($entry ? $this->makeReportNumber($batch, $entry) : ('ER-'.$batch->course_id.'-'.$batch->id.'-'.$eraport->user_id))
        );

        // verify_url wajib
        $verifyUrl = data_get($payload, 'eraport.verify_url');
        if (!$verifyUrl) {
            try {
                $verifyUrl = route('public.eraport.verify', ['token' => $eraport->verify_token]);
            } catch (\Throwable $e) {
                $verifyUrl = url('/eraport/verify/'.$eraport->verify_token);
            }
            data_set($payload, 'eraport.verify_url', $verifyUrl);
        }

        return $payload;
    }


    // ✅ payload HARUS match dataBindings pada field_map
    private function buildSnapshotPayload(EraportBatch $batch, EraportEntry $entry, Eraport $eraport): array
    {
        $course = $batch->course;

        $studentName = DB::table('users')->where('id', $entry->user_id)->value('name');

        $enr = DB::table('enrollments')
            ->where('course_id', $batch->course_id)
            ->where('user_id', $entry->user_id)
            ->first();

        $schoolName = null;
        if (!empty($enr?->sekolah_id)) {
            $schoolName = DB::table('schools')->where('id', $enr->sekolah_id)->value('name');
        }

        // ✅ kelas_label jangan query kolom yang tidak ada
        $kelasLabel = $enr->kelas_label ?? $enr->kelas ?? null;

        // kalau mau fallback ke users, cek dulu kolomnya ada
        if (!$kelasLabel) {
            if (Schema::hasColumn('users', 'kelas_label')) {
                $kelasLabel = DB::table('users')->where('id', $entry->user_id)->value('kelas_label');
            } elseif (Schema::hasColumn('users', 'kelas')) {
                $kelasLabel = DB::table('users')->where('id', $entry->user_id)->value('kelas');
            }
        }

        $payload = [
            'student' => [
                'name' => $studentName ?: ('User #'.$entry->user_id),
                'kelas_label' => $kelasLabel, // boleh null
            ],
            'semester' => [
                'label' => $batch->semester_label,
            ],
            'school' => [
                'name' => $schoolName ?: '-',
            ],
            'course' => [
                'title' => $course->nama_kelas ?? ($course->deskripsi ?? '-'),
                'platform' => $entry->platform ?? '-',
                'category' => $entry->category ?? '-',
            ],
            'attendance' => [
                'summary' => [
                    'hadir' => (int)$entry->hadir_count,
                    'sakit' => (int)$entry->sakit_count,
                    'izin'  => (int)$entry->izin_count,
                    'alpha' => (int)$entry->alpha_count,
                ],
            ],
            'scores' => [
                'avg_project' => $entry->avg_project_score,
                'logic_ct'    => $entry->logic_predicate ?? $entry->logic_score,
                'creativity'  => $entry->creativity_predicate ?? $entry->creativity_score,
            ],
            'mentor_note' => [
                'note' => $entry->mentor_note,
            ],
            'eraport' => [
                'number' => $eraport->report_number ?: $this->makeReportNumber($batch, $entry),
                'verify_url' => route('public.eraport.verify', ['token' => $eraport->verify_token]),
            ],
        ];

        return $payload;
    }

    private function generatePdfFromTemplate(EraportBatch $batch, Eraport $eraport, array $payload, ?EraportEntry $entry = null): ?string
    {
        try {
            $template = $batch->template;
            if (!$template) {
                Log::error('Template null di batch', ['batch_id' => $batch->id]);
                return null;
            }

            $fieldMapRaw = $template->field_map ?? [];
            if (is_string($fieldMapRaw)) {
                $fieldMap = json_decode($fieldMapRaw, true) ?: [];
            } elseif (is_array($fieldMapRaw)) {
                $fieldMap = $fieldMapRaw;
            } else {
                $fieldMap = [];
            }

            if (empty($fieldMap)) {
                Log::error('field_map kosong/invalid', ['template_id' => $template->id]);
                return null;
            }

            $bgPath = $template->background_path ?: data_get($fieldMap, 'template.background.path');
            if (!$bgPath) {
                Log::error('Background path tidak ada', ['template_id' => $template->id]);
                return null;
            }

            $bgPath = preg_replace('#^/?storage/#', '', (string)$bgPath);
            $bgPath = ltrim($bgPath, '/');

            $disk = 'public';
            if (!Storage::disk($disk)->exists($bgPath)) {
                Log::error('File background tidak ditemukan', [
                    'template_id' => $template->id,
                    'disk' => $disk,
                    'bgPath' => $bgPath,
                    'abs' => Storage::disk($disk)->path($bgPath),
                ]);
                return null;
            }

            $backgroundDataUri = $this->toDataUriFromStorage($disk, $bgPath);

            $view = 'eraport.pdf.render_png';

            if (!view()->exists($view)) {
                Log::error('View render PNG tidak ditemukan', [
                    'view' => $view,
                    'template_id' => $template->id,
                    'hint' => 'Pastikan file ada di resources/views/eraport/pdf/render_png.blade.php',
                ]);
                return null;
            }

            // ✅ pastikan payload punya verify_url & number
            $payload = $this->injectEraportMetaToPayload($payload, $batch, $entry, $eraport);

            $verifyUrl = data_get($payload, 'eraport.verify_url');

            // generate QR
            $qrDataUri = null;
            try {
                $png = QrCode::format('png')
                    ->size(320)
                    ->margin(1)
                    ->errorCorrection('M')
                    ->generate($verifyUrl);

                $qrDataUri = 'data:image/png;base64,' . base64_encode($png);
            } catch (\Throwable $e) {
                Log::error('QR gen failed', [
                    'msg' => $e->getMessage(),
                    'verify_url' => $verifyUrl,
                ]);
                $qrDataUri = null;
            }

            $pdf = Pdf::loadView($view, [
                    'fieldMap' => $fieldMap,
                    'payload'  => $payload,
                    'backgroundDataUri' => $backgroundDataUri,
                    'qrDataUri' => $qrDataUri,
                ])
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);

            $out = $pdf->output();

            $savePath = "eraport/pdfs/batch_{$batch->id}/eraport_{$eraport->id}.pdf";
            Storage::disk('public')->put($savePath, $out);

            return $savePath;

        } catch (\Throwable $e) {
            Log::error('PDF gen failed: '.$e->getMessage(), [
                'eraport_id' => $eraport->id ?? null,
                'batch_id' => $batch->id ?? null,
            ]);
            return null;
        }
    }

}
