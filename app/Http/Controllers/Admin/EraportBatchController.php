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
    /* ============================================================
       LIST / CRUD BATCH
    ============================================================ */

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

    public function create()
    {
        $courses   = Course::query()->orderBy('nama_kelas', 'asc')->get();
        $templates = EraportTemplate::query()->orderByDesc('id')->get();

        $semesters = Semester::query()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        return view('admin.eraport.batches.create', compact('courses', 'templates', 'semesters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => 'required|integer',
            'template_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $semester = Semester::query()->findOrFail($data['semester_id']);
        $semesterLabel = trim($semester->name . ' ' . $semester->year);

        $batch = EraportBatch::create([
            'course_id'      => $data['course_id'],
            'template_id'    => $data['template_id'],
            'semester_id'    => $semester->id,
            'semester_label' => $semesterLabel,
            'notes'          => $data['notes'] ?? null,
            'status'         => 'DRAFT',          // ✅ kapital
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch dibuat.');
    }

    public function show(EraportBatch $batch)
    {
        $batch->load(['course', 'template']);

        $entries = EraportEntry::query()
            ->where('batch_id', $batch->id)
            ->orderBy('user_id')
            ->paginate(25);

        $summary = $this->buildValidationSummary($batch);

        $eraportMap = Eraport::query()
            ->where('batch_id', $batch->id)
            ->select('id', 'user_id', 'pdf_path', 'status')
            ->get()
            ->keyBy('user_id');

        return view('admin.eraport.batches.show', compact('batch', 'entries', 'summary', 'eraportMap'));
    }

    /* ============================================================
       VALIDATE / SYNC
    ============================================================ */

    public function validateBatch(EraportBatch $batch)
    {
        $this->ensureNotPublished($batch);

        DB::transaction(function () use ($batch) {
            $batch->update([
                'status' => 'VALIDATING', // ✅ kapital
            ]);

            $this->syncEntriesFromSource($batch->id, $batch->course_id);

            $missing = $this->buildMissingMap($batch);

            $batch->update([
                'status' => empty($missing) ? 'READY' : 'DRAFT', // ✅ kapital
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

    private function syncEntriesFromSource(int $batchId, int $courseId): void
    {
        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        $participants = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->whereRaw("LOWER(status)='aktif'")
            ->select('user_id', 'mentor_id')
            ->get()
            ->keyBy('user_id');

        $userIds = $participants->keys()->map(fn($v) => (int)$v)->values();
        if ($userIds->isEmpty()) return;

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
                'mentor_id' => $enr->mentor_id ?? null,

                'avg_project_score' => $s?->avg_project_score,
                'logic_score' => $logic,
                'logic_predicate' => $this->predicate($logic),
                'creativity_score' => $crea,
                'creativity_predicate' => $this->predicate($crea),

                'hadir_count' => (int)($a->hadir_count ?? 0),
                'sakit_count' => (int)($a->sakit_count ?? 0),
                'izin_count'  => (int)($a->izin_count  ?? 0),
                'alpha_count' => (int)($a->alpha_raw ?? 0) + $missing,

                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

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

    private function buildMissingMap(EraportBatch $batch): array
    {
        $courseId = $batch->course_id;

        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        $userIds = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->whereRaw("LOWER(status)='aktif'")
            ->pluck('user_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        if (empty($userIds)) return [];

        $hasScoreIds = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->where('m.course_id', $courseId)
            ->whereNull('s.deleted_at')
            ->groupBy('s.peserta_id')
            ->pluck('s.peserta_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $hasScoreSet = array_flip($hasScoreIds);

        $attCount = DB::table('attendances')
            ->where('course_id', $courseId)
            ->whereNotNull('meeting_id')
            ->whereNull('deleted_at')
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(DISTINCT meeting_id) as c')
            ->get()
            ->keyBy('user_id');

        $missing = [];

        foreach ($userIds as $uid) {
            $issues = [];

            if (!isset($hasScoreSet[$uid])) {
                $issues[] = 'Nilai belum ada (scores)';
            }

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

    private function buildValidationSummary(EraportBatch $batch): array
    {
        $courseId = $batch->course_id;

        $totalMeetings = (int) DB::table('meetings')
            ->where('course_id', $courseId)
            ->count();

        $participants = DB::table('enrollments')
            ->where('course_id', $courseId)
            ->whereRaw("LOWER(status)='aktif'")
            ->pluck('user_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $hasScore = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->where('m.course_id', $courseId)
            ->whereNull('s.deleted_at')
            ->groupBy('s.peserta_id')
            ->pluck('s.peserta_id')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $missingScores = array_values(array_diff($participants, $hasScore));

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

    /* ============================================================
       PUBLISH / REOPEN
    ============================================================ */

    public function publish(EraportBatch $batch)
    {
        $this->ensureNotPublished($batch);

        DB::transaction(function () use ($batch) {
            $this->syncEntriesFromSource($batch->id, $batch->course_id);

            $summary = $this->buildValidationSummary($batch);
            if ($summary['missing_scores_count'] > 0 || $summary['missing_attendance_count'] > 0) {
                abort(422, 'Masih ada data kosong. Selesaikan dulu sebelum publish.');
            }

            EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->update([
                    'locked_at' => now(),
                    'locked_by' => Auth::id(),
                    'updated_at' => now(),
                ]);

            $entries = EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->get();

            foreach ($entries as $entry) {
                $reportNumber = $this->makeReportNumber($batch, $entry);
                $token = Str::random(32);

                // ✅ Buat rapor dulu
                $eraport = Eraport::create([
                    'batch_id'      => $batch->id,
                    'user_id'       => $entry->user_id,
                    'report_number' => $reportNumber,
                    'verify_token'  => $token,
                    'snapshot_json' => json_encode([], JSON_UNESCAPED_UNICODE),
                    'status'        => 'PUBLISHED',     // ✅ kapital
                    'published_at'  => now(),
                    'published_by'  => Auth::id(),      // kalau kolom ada
                ]);

                // ✅ Build payload final
                $payload = $this->buildSnapshotPayload($batch, $entry, $eraport);

                $eraport->update([
                    'snapshot_json' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                ]);

                // ✅ Generate PDF (FIX: page-003.jpg)
                $pdfPath = $this->generatePdfFromPage003($batch, $eraport, $payload);
                if ($pdfPath) {
                    $eraport->update(['pdf_path' => $pdfPath]);
                }
            }

            $batch->update([
                'status' => 'PUBLISHED',  // ✅ kapital
                'published_at' => now(),
            ]);
        });

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch berhasil diterbitkan (publish).');
    }

    public function reopen(EraportBatch $batch)
    {
        if ($batch->status !== 'PUBLISHED') {
            return back()->with('error', 'Batch belum publish.');
        }

        DB::transaction(function () use ($batch) {
            EraportEntry::query()
                ->where('batch_id', $batch->id)
                ->update([
                    'locked_at' => null,
                    'locked_by' => null,
                    'updated_at' => now(),
                ]);

            Eraport::query()->where('batch_id', $batch->id)->delete();

            $batch->update([
                'status' => 'DRAFT', // ✅ kapital
                'reopened_at' => now(),
                'published_at' => null,
            ]);
        });

        return redirect()->route('admin.eraport.batches.show', $batch->id)
            ->with('success', 'Batch dibuka kembali (reopen). Silakan revisi nilai/absensi.');
    }

    /* ============================================================
       DOWNLOAD SINGLE ENTRY PDF (ON-DEMAND)
    ============================================================ */

    public function downloadEntryPdf(EraportBatch $batch, EraportEntry $entry)
    {
        if ((int)$entry->batch_id !== (int)$batch->id) abort(404);

        $eraport = Eraport::query()
            ->where('batch_id', $batch->id)
            ->where('user_id', $entry->user_id)
            ->latest('id')
            ->first();

        if (!$eraport) {
            $eraport = Eraport::create([
                'batch_id'      => $batch->id,
                'user_id'       => $entry->user_id,
                'report_number' => $this->makeReportNumber($batch, $entry),
                'verify_token'  => Str::random(32),
                'snapshot_json' => json_encode([], JSON_UNESCAPED_UNICODE),
                'status'        => 'PUBLISHED', // ✅ kapital
                'published_at'  => now(),
                'published_by'  => Auth::id(),
            ]);
        }

        if ($eraport->pdf_path && Storage::disk('public')->exists($eraport->pdf_path)) {
            return Storage::disk('public')->download(
                $eraport->pdf_path,
                Str::slug($eraport->report_number ?: 'eraport-'.$eraport->id).'.pdf'
            );
        }

        $payload = json_decode($eraport->snapshot_json ?? '', true);
        if (!is_array($payload)) $payload = [];

        if ($this->payloadNeedsUpgrade($payload)) {
            $payload = $this->buildSnapshotPayload($batch, $entry, $eraport);
        } else {
            $payload = $this->injectEraportMetaToPayload($payload, $batch, $entry, $eraport);
        }

        $eraport->update(['snapshot_json' => json_encode($payload, JSON_UNESCAPED_UNICODE)]);

        // ✅ On-demand juga pakai generator yang sama (page-003.jpg)
        $pdfPath = $this->generatePdfFromPage003($batch, $eraport, $payload);

        if (!$pdfPath || !Storage::disk('public')->exists($pdfPath)) {
            Log::error('PDF gagal dibuat (on-demand)', [
                'batch_id' => $batch->id,
                'user_id'  => $entry->user_id,
                'eraport_id' => $eraport->id,
            ]);
            return back()->with('error', 'PDF gagal dibuat. Cek view page003_table + file background + font Montserrat.');
        }

        $eraport->update(['pdf_path' => $pdfPath]);

        return Storage::disk('public')->download(
            $pdfPath,
            Str::slug($eraport->report_number ?: 'eraport-'.$eraport->id).'.pdf'
        );
    }

    /* ============================================================
       EXPORT ZIP
    ============================================================ */

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
       PDF GENERATOR FIX: BACKGROUND page-003.jpg + VIEW TABLE
    ============================================================ */

    private function generatePdfFromPage003(EraportBatch $batch, Eraport $eraport, array $payload): ?string
    {
        try {
            // ✅ background (sesuai request)
            $bgPath = 'eraport/templates/page-003.jpg';
            $disk = 'public';

            if (!Storage::disk($disk)->exists($bgPath)) {
                Log::error('BG page-003.jpg tidak ditemukan', [
                    'bgPath' => $bgPath,
                    'abs' => Storage::disk($disk)->path($bgPath),
                ]);
                return null;
            }

            $backgroundDataUri = $this->toDataUriFromStorage($disk, $bgPath);

            // ✅ pastikan verify_url ada
            $verifyUrl = data_get($payload, 'eraport.verify_url');
            if (!$verifyUrl) {
                try {
                    $verifyUrl = route('public.eraport.verify', ['token' => $eraport->verify_token]);
                } catch (\Throwable $e) {
                    $verifyUrl = url('/eraport/verify/'.$eraport->verify_token);
                }
                data_set($payload, 'eraport.verify_url', $verifyUrl);
            }

            $verifyUrl = data_get($payload, 'eraport.verify_url');

            // ✅ QR SVG -> Data URI (NO imagick)
            $qrDataUri = null;

            if (!empty($verifyUrl)) {
                try {
                    $svg = QrCode::format('svg')
                        ->size(240)
                        ->margin(0)
                        ->errorCorrection('M')
                        ->generate($verifyUrl);

                    // optional: buang XML header
                    $svg = preg_replace('/<\?xml.*?\?>/m', '', $svg);

                    $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

                    Log::info('QR_OK', [
                        'verify_url' => $verifyUrl,
                        'svg_len' => strlen($svg),
                    ]);
                } catch (\Throwable $e) {
                    Log::error('QR gen failed', [
                        'msg' => $e->getMessage(),
                        'verify_url' => $verifyUrl,
                    ]);
                    $qrDataUri = null;
                }
            } else {
                Log::warning('QR verify_url kosong', [
                    'batch_id' => $batch->id,
                    'eraport_id' => $eraport->id,
                ]);
            }

            // ✅ view wajib diset
            $view = 'eraport.pdf.page003_table';

            if (!view()->exists($view)) {
                Log::error('View page003_table tidak ditemukan', [
                    'view' => $view,
                    'hint' => 'Pastikan: resources/views/eraport/pdf/page003_table.blade.php',
                ]);
                return null;
            }

            $pdf = Pdf::loadView($view, [
                    'payload' => $payload,
                    'backgroundDataUri' => $backgroundDataUri,
                    'qrDataUri' => $qrDataUri,
                ])
                ->setPaper('a4', 'portrait')
                ->setOption('dpi', 96)
                ->setOption('isRemoteEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);

            $out = $pdf->output();

            $savePath = "eraport/pdfs/batch_{$batch->id}/eraport_{$eraport->id}.pdf";
            Storage::disk('public')->put($savePath, $out);

            return $savePath;

        } catch (\Throwable $e) {
            Log::error('PDF gen page003 failed: '.$e->getMessage(), [
                'batch_id' => $batch->id ?? null,
                'eraport_id' => $eraport->id ?? null,
            ]);
            return null;
        }
    }

    /* ============================================================
       PAYLOAD
    ============================================================ */

    private function payloadNeedsUpgrade(array $payload): bool
    {
        return
            data_get($payload, 'student.name') === null ||
            data_get($payload, 'attendance.summary.hadir') === null ||
            data_get($payload, 'scores.avg_project') === null ||
            data_get($payload, 'eraport.verify_url') === null;
    }

    private function injectEraportMetaToPayload(array $payload, EraportBatch $batch, EraportEntry $entry, Eraport $eraport): array
    {
        data_set($payload, 'eraport.number', $eraport->report_number ?: $this->makeReportNumber($batch, $entry));

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

    private function buildSnapshotPayload(EraportBatch $batch, EraportEntry $entry, Eraport $eraport): array
    {
        $course = $batch->course;

        $studentName = DB::table('users')->where('id', $entry->user_id)->value('name');

        // ✅ TAMBAHAN: ambil kelas dari users.kelas_id -> kelas.nama
        $kelasFromMaster = null;
        if (Schema::hasColumn('users', 'kelas_id')) {
            $kelasId = DB::table('users')->where('id', $entry->user_id)->value('kelas_id');

            if (!empty($kelasId)) {
                // ganti 'kelas' & 'nama' jika nama tabel/kolom berbeda
                $kelasFromMaster = DB::table('kelas')->where('id', $kelasId)->value('nama');
            }
        }

        $enr = DB::table('enrollments')
            ->where('course_id', $batch->course_id)
            ->where('user_id', $entry->user_id)
            ->first();

        $schoolName = null;
        if (!empty($enr?->sekolah_id)) {
            $schoolName = DB::table('sekolah')->where('id', $enr->sekolah_id)->value('nama_sekolah');
        }

        $kelasLabel = $enr->kelas_label ?? $enr->kelas ?? null;

        // ✅ PRIORITAS: kalau kelas master ada, pakai itu
        if (!empty($kelasFromMaster)) {
            $kelasLabel = $kelasFromMaster;
        }

        // fallback lama kamu tetap dipakai kalau masih kosong
        if (!$kelasLabel) {
            if (Schema::hasColumn('users', 'kelas_label')) {
                $kelasLabel = DB::table('users')->where('id', $entry->user_id)->value('kelas_label');
            } elseif (Schema::hasColumn('users', 'kelas')) {
                $kelasLabel = DB::table('users')->where('id', $entry->user_id)->value('kelas');
            }
        }

        return [
            'student' => [
                'name' => $studentName ?: ('User #'.$entry->user_id),
                'kelas_label' => $kelasLabel ?: '-',
            ],
            'semester' => [
                'label' => $batch->semester_label ?: '-',
            ],
            'school' => [
                'name' => $schoolName ?: '-',
            ],
            'course' => [
                'title' => $course->nama_kelas ?? ($course->deskripsi ?? '-'),
                'platform' => $entry->platform ?? ($course->platform ?? '-'),
                'category' => $entry->category ?? ($course->category ?? '-'),
            ],
            'attendance' => [
                'summary' => [
                    'hadir' => (int)($entry->hadir_count ?? 0),
                    'sakit' => (int)($entry->sakit_count ?? 0),
                    'izin'  => (int)($entry->izin_count  ?? 0),
                    'alpha' => (int)($entry->alpha_count ?? 0),
                ],
            ],
            'scores' => [
                'avg_project' => $entry->avg_project_score ?? '-',
                'logic_ct'    => $entry->logic_predicate ?? ($entry->logic_score ?? '-'),
                'creativity'  => $entry->creativity_predicate ?? ($entry->creativity_score ?? '-'),
            ],
            'mentor_note' => [
                'note' => $entry->mentor_note ?? '-',
            ],
            'eraport' => [
                'number' => $eraport->report_number ?: $this->makeReportNumber($batch, $entry),
                'verify_url' => route('public.eraport.verify', ['token' => $eraport->verify_token]),
            ],
        ];
    }


    /* ============================================================
       HELPERS
    ============================================================ */

    private function predicate(?float $score): ?string
    {
        if ($score === null) return null;
        if ($score >= 95) return 'EXCELLENT';
        if ($score >= 90) return 'VERY GOOD';
        if ($score >= 80) return 'GOOD';
        return 'AVERAGE';
    }

    private function makeReportNumber(EraportBatch $batch, EraportEntry $entry): string
    {
        return 'ER-' . $batch->course_id . '-' . $batch->id . '-' . $entry->user_id;
    }

    private function ensureNotPublished(EraportBatch $batch): void
    {
        if ($batch->status === 'PUBLISHED') {
            abort(422, 'Batch sudah publish. Reopen dulu jika ingin revisi.');
        }
    }

    private function toDataUriFromStorage(string $disk, string $path): string
    {
        $bytes = Storage::disk($disk)->get($path);
        $mime  = Storage::disk($disk)->mimeType($path) ?: 'image/jpeg';
        $b64   = base64_encode($bytes);
        return "data:{$mime};base64,{$b64}";
    }
}
