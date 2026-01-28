<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InfografisController extends Controller
{
    public function index(Request $request)
    {
        $semesterId = $request->integer('semester_id');
        $sekolahId  = $request->integer('sekolah_id');
        $courseId   = $request->integer('course_id');

        // Dropdown data
        $semesters = DB::table('semesters')
            ->orderByDesc('is_active')
            ->orderByDesc('year')
            ->orderBy('name')
            ->get();

        $schools = DB::table('sekolah')
            ->whereNull('deleted_at')
            ->orderBy('nama_sekolah')
            ->get();

        // Semester terpilih
        $selectedSemester = $semesterId
            ? DB::table('semesters')->where('id', $semesterId)->first()
            : null;

        $start = $selectedSemester->start_date ?? null;
        $end   = $selectedSemester->end_date ?? null;

        // Rule: jika ALL kursus (course kosong) maka sekolah wajib dipilih
        $mustPickSchoolForAllCourse = false;
        if ($semesterId && !$courseId && !$sekolahId) {
            $mustPickSchoolForAllCourse = true;
        }

        // Courses list (berdasarkan meeting di range semester)
        $courses = [];
        if ($semesterId && $start && $end) {
            $courseQuery = DB::table('courses as c')
                ->whereNull('c.deleted_at')
                ->leftJoin('meetings as m', 'm.course_id', '=', 'c.id')
                ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
                ->select('c.id', 'c.nama_kelas')
                ->distinct()
                ->orderBy('c.nama_kelas');

            if ($sekolahId) {
                $courseQuery->where('c.sekolah_id', $sekolahId);
            }

            $courses = $courseQuery->get();
        }

        // Nama terpilih
        $selectedSchoolName = $sekolahId
            ? (DB::table('sekolah')->where('id', $sekolahId)->value('nama_sekolah') ?? ("Sekolah#".$sekolahId))
            : 'ALL';

        $selectedCourseName = $courseId
            ? (DB::table('courses')->where('id', $courseId)->value('nama_kelas') ?? ("Kursus#".$courseId))
            : 'ALL (Agregat)';

        // Default output
        $cards = [
            ['label' => 'Jumlah Pertemuan', 'value' => 0],
            ['label' => 'Peserta Aktif', 'value' => 0],
            ['label' => 'Total Peserta Terlibat', 'value' => 0],
        ];

        $kehadiran = [
            'total' => 0, 'hadir' => 0, 'tidak_hadir' => 0, 'izin' => 0, 'sakit' => 0,
            'total_tidak_hadir' => 0, 'rate_hadir' => 0,
        ];

        $trendCombined = [];
        $courseSummaryRows = [];
        $participants = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10); // paginator nanti
        $courseMeta = null;
        $courseSummary = null;

        // Hitung data hanya jika semester valid & tidak melanggar rule
        if ($semesterId && $start && $end && !$mustPickSchoolForAllCourse) {

            if ($courseId) {
                $courseMeta = $this->getCourseMeta($courseId);
            }

            $cards = $this->buildSummaryCards($start, $end, $sekolahId, $courseId);
            $kehadiran = $this->buildAttendanceSummary($start, $end, $sekolahId, $courseId);
            $trendCombined = $this->buildTrendCombined($start, $end, $sekolahId, $courseId);

            if (!$courseId && $sekolahId) {
                $courseSummaryRows = $this->buildCourseSummaryRows($start, $end, $sekolahId);
            }

            $participants = $this->buildParticipantsTable($request, $start, $end, $sekolahId, $courseId);
        }

        $summaryText = null;
        $summaryApprovedAt = null;
        $summaryApprovedBy = null;

        if (!empty($semesterId) && !empty($courseId)) {
            $row = DB::table('course_summaries')
                ->where('semester_id', (int)$semesterId)
                ->where('course_id', (int)$courseId)
                ->select('summary_text','approved_at','approved_by')
                ->first();

            $summaryText = $row->summary_text ?? null;
            $summaryApprovedAt = $row->approved_at ?? null;
            $summaryApprovedBy = $row->approved_by ?? null;
        }


        return view('admin.infografis.index', compact(
            'semesters',
            'schools',
            'courses',
            'semesterId',
            'sekolahId',
            'courseId',
            'selectedSemester',
            'selectedSchoolName',
            'selectedCourseName',
            'mustPickSchoolForAllCourse',
            'cards',
            'kehadiran',
            'trendCombined',
            'courseSummaryRows',
            'participants',
            'courseMeta',
            'summaryText',
            'summaryApprovedAt',
            'summaryApprovedBy'
        ));
    }

    /**
     * Export PDF (tanpa absensi mentor)
     * NOTE: Chart JS tidak bisa ikut. Diagram harus statis via HTML/CSS di view PDF.
     */
    public function export(Request $request)
    {
        $semesterId = $request->integer('semester_id');
        $sekolahId  = $request->integer('sekolah_id');
        $courseId   = $request->integer('course_id');

        if (!$semesterId) {
            return back()->with('error', 'Semester wajib dipilih untuk export PDF.');
        }

        $selectedSemester = DB::table('semesters')->where('id', $semesterId)->first();
        if (!$selectedSemester || empty($selectedSemester->start_date) || empty($selectedSemester->end_date)) {
            return back()->with('error', 'Semester tidak valid / belum memiliki start_date dan end_date.');
        }

        $start = $selectedSemester->start_date;
        $end   = $selectedSemester->end_date;

        // Rule: jika ALL course, sekolah wajib dipilih (biar PDF tidak ambigu)
        if (!$courseId && !$sekolahId) {
            return back()->with('error', 'Untuk export mode ALL (Agregat), pilih Sekolah terlebih dahulu.');
        }

        $selectedSchoolName = $sekolahId
            ? (DB::table('sekolah')->where('id', $sekolahId)->value('nama_sekolah') ?? ("Sekolah#".$sekolahId))
            : 'ALL';

        $selectedCourseName = $courseId
            ? (DB::table('courses')->where('id', $courseId)->value('nama_kelas') ?? ("Kursus#".$courseId))
            : 'ALL (Agregat)';

        // Build data (pakai helper kamu yang sudah ada)
        $cards = $this->buildSummaryCards($start, $end, $sekolahId, $courseId);
        $kehadiran = $this->buildAttendanceSummary($start, $end, $sekolahId, $courseId);
        $trendCombined = $this->buildTrendCombined($start, $end, $sekolahId, $courseId);

        $courseSummaryRows = [];
        if (!$courseId && $sekolahId) {
            $courseSummaryRows = $this->buildCourseSummaryRows($start, $end, $sekolahId);
        }

        $courseMeta = null;
        if ($courseId) {
            $courseMeta = $this->getCourseMeta($courseId);
        }

        // Participants: ambil full (tanpa paginator) agar PDF rapi
        // Kamu bisa batasi max rows supaya PDF tidak terlalu panjang (mis. 200)
        $participants = $this->buildParticipantsTableForPdf($start, $end, $sekolahId, $courseId, 250);

        // Resume (course_summaries)
        $summaryText = null;
        $summaryApprovedAt = null;
        $summaryApprovedBy = null;

        if ($semesterId && $courseId) {
            $row = DB::table('course_summaries')
                ->where('semester_id', (int)$semesterId)
                ->where('course_id', (int)$courseId)
                ->select('summary_text','approved_at','approved_by')
                ->first();

            $summaryText = $row->summary_text ?? null;
            $summaryApprovedAt = $row->approved_at ?? null;
            $summaryApprovedBy = $row->approved_by ?? null;
        }

        $pdf = Pdf::loadView('admin.infografis.pdf', compact(
            'semesterId',
            'sekolahId',
            'courseId',
            'selectedSemester',
            'selectedSchoolName',
            'selectedCourseName',
            'cards',
            'kehadiran',
            'trendCombined',
            'courseSummaryRows',
            'participants',
            'courseMeta',
            'summaryText',
            'summaryApprovedAt',
            'summaryApprovedBy'
        ))->setPaper('a4', 'portrait');

        $filename = 'Infografis_Semester-'.$semesterId.'_Sekolah-'.($sekolahId ?: 'ALL').'_Course-'.($courseId ?: 'ALL').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Khusus PDF: ambil data peserta full (tanpa links()).
     * $limit bisa kamu atur.
     */
    private function buildParticipantsTableForPdf(string $start, string $end, ?int $sekolahId, ?int $courseId, int $limit = 250)
    {
        // Ini meniru logika buildParticipantsTable() kamu,
        // tapi outputnya ->get() bukan paginate().
        // (Aku bikin versi ringkas dan aman)

        // subquery avg score per peserta
        $scoreAgg = DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->whereNull('s.deleted_at')
            ->where('u.role_id', 3)
            ->selectRaw("s.peserta_id as peserta_id, ROUND(AVG(s.total_score),1) as avg_score")
            ->groupBy('s.peserta_id');

        if ($sekolahId) $scoreAgg->where('u.sekolah_id', $sekolahId);

        if ($courseId) {
            $scoreAgg->join('meetings as m', 'm.id', '=', 's.meeting_id')
                ->where('m.course_id', $courseId)
                ->whereBetween('m.tanggal_pelaksanaan', [$start, $end]);
        } else {
            if ($sekolahId) {
                $scoreAgg->join('meetings as m', 'm.id', '=', 's.meeting_id')
                    ->join('courses as c', 'c.id', '=', 'm.course_id')
                    ->where('c.sekolah_id', $sekolahId)
                    ->whereBetween('m.tanggal_pelaksanaan', [$start, $end]);
            } else {
                $scoreAgg->whereRaw('1=0');
            }
        }

        $base = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'u.kelas_id')
            ->leftJoin('courses as c', 'c.id', '=', 'a.course_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->whereBetween('a.tanggal', [$start, $end]);

        if ($sekolahId) $base->where('u.sekolah_id', $sekolahId);
        if ($courseId)  $base->where('a.course_id', $courseId);

        $base->leftJoinSub($scoreAgg, 'sc', fn($j) => $j->on('sc.peserta_id','=','u.id'));

        return $base->selectRaw("
            u.id as user_id,
            u.name,
            k.nama as kelas_name,
            c.nama_kelas as course_name,
            COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir,
            COUNT(DISTINCT CASE WHEN a.status='Tidak Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as tidak_hadir,
            COUNT(DISTINCT CASE WHEN a.status='Izin' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as izin,
            COUNT(DISTINCT CASE WHEN a.status='Sakit' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as sakit,
            COALESCE(sc.avg_score, 0) as avg_score
        ")
        ->groupBy('u.id', 'u.name', 'k.nama', 'c.nama_kelas', 'sc.avg_score')
        ->orderByDesc('avg_score')
        ->limit($limit)
        ->get();
    }

    // =========================
    // Resume Course Summary and Unapprove
    // =========================
    public function saveResume(Request $request)
    {
        $data = $request->validate([
            'semester_id'  => ['required','integer'],
            'course_id'    => ['required','integer'],
            'summary_text' => ['nullable','string'],
        ]);

        $row = DB::table('course_summaries')
            ->where('semester_id', $data['semester_id'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($row) {
            // Update: kalau sudah pernah approved, reset approval agar butuh approve ulang
            $resetApproval = !empty($row->approved_at) || !empty($row->approved_by);

            DB::table('course_summaries')
                ->where('id', $row->id)
                ->update([
                    'summary_text' => $data['summary_text'],
                    'updated_at'   => now(),
                    'approved_at'  => $resetApproval ? null : $row->approved_at,
                    'approved_by'  => $resetApproval ? null : $row->approved_by,
                ]);
        } else {
            DB::table('course_summaries')->insert([
                'semester_id'  => $data['semester_id'],
                'course_id'    => $data['course_id'],
                'summary_text' => $data['summary_text'],
                'approved_by'  => null,
                'approved_at'  => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return back()->with('success', 'Draft resume tersimpan. Jika sebelumnya sudah approve, status kembali menjadi draft.');
    }

    public function approveResume(Request $request)
    {
        $data = $request->validate([
            'semester_id'  => ['required','integer'],
            'course_id'    => ['required','integer'],
            'summary_text' => ['nullable','string'], // optional: supaya bisa update lalu approve
        ]);

        $row = DB::table('course_summaries')
            ->where('semester_id', $data['semester_id'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($row) {
            DB::table('course_summaries')
                ->where('id', $row->id)
                ->update([
                    'summary_text' => array_key_exists('summary_text', $data) ? $data['summary_text'] : $row->summary_text,
                    'approved_by'  => auth()->id(),
                    'approved_at'  => now(),
                    'updated_at'   => now(),
                ]);
        } else {
            DB::table('course_summaries')->insert([
                'semester_id'  => $data['semester_id'],
                'course_id'    => $data['course_id'],
                'summary_text' => $data['summary_text'] ?? null,
                'approved_by'  => auth()->id(),
                'approved_at'  => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return back()->with('success', 'Resume berhasil di-approve.');
    }

    public function unapproveResume(Request $request)
    {
        $data = $request->validate([
            'semester_id' => ['required','integer'],
            'course_id'   => ['required','integer'],
        ]);

        DB::table('course_summaries')
            ->where('semester_id', $data['semester_id'])
            ->where('course_id', $data['course_id'])
            ->update([
                'approved_by' => null,
                'approved_at' => null,
                'updated_at'  => now(),
            ]);

        return back()->with('success', 'Status approve dibatalkan. Resume kembali menjadi draft.');
    }


    // =========================
    // Helpers
    // =========================

    private function getCourseMeta(int $courseId): array
    {
        $c = DB::table('courses')->where('id', $courseId)->first();
        if (!$c) return [
            'mentor_names' => [],
            'waktu_mulai' => null,
            'waktu_akhir' => null,
        ];

        $mentorIds = collect([$c->mentor_id ?? null, $c->mentor_id_2 ?? null, $c->mentor_id_3 ?? null])
            ->filter()
            ->unique()
            ->values();

        $mentorNames = [];
        if ($mentorIds->count()) {
            $mentorNames = DB::table('users')
                ->whereIn('id', $mentorIds->all())
                ->pluck('name')
                ->toArray();
        }

        return [
            'mentor_names' => $mentorNames,
            'waktu_mulai' => $c->waktu_mulai ?? null,
            'waktu_akhir' => $c->waktu_akhir ?? null,
        ];
    }

    private function scopeCourseMeetingRange(string $start, string $end, ?int $sekolahId, ?int $courseId)
    {
        $q = DB::table('courses as c')
            ->join('meetings as m', 'm.course_id', '=', 'c.id')
            ->whereNull('c.deleted_at')
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end]);

        if ($sekolahId) $q->where('c.sekolah_id', $sekolahId);
        if ($courseId)  $q->where('c.id', $courseId);

        return $q->distinct()->pluck('c.id');
    }

    private function buildSummaryCards(string $start, string $end, ?int $sekolahId, ?int $courseId): array
    {
        $courseIds = $this->scopeCourseMeetingRange($start, $end, $sekolahId, $courseId);
        if ($courseIds->isEmpty()) {
            return [
                ['label' => 'Jumlah Pertemuan', 'value' => 0],
                ['label' => 'Peserta Aktif', 'value' => 0],
                ['label' => 'Total Peserta Terlibat', 'value' => 0],
            ];
        }

        $meetingCount = DB::table('meetings as m')
            ->whereIn('m.course_id', $courseIds)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->distinct('m.id')
            ->count('m.id');

        $pesertaAktif = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('meetings as m', 'm.id', '=', 'a.meeting_id')
            ->join('courses as c', 'c.id', '=', 'm.course_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->when($sekolahId, fn($q) => $q->where('u.sekolah_id', $sekolahId))
            ->whereIn('c.id', $courseIds)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->distinct('u.id')
            ->count('u.id');

        $totalPeserta = $pesertaAktif;

        return [
            ['label' => 'Jumlah Pertemuan', 'value' => $meetingCount],
            ['label' => 'Peserta Aktif', 'value' => $pesertaAktif],
            ['label' => 'Total Peserta Terlibat', 'value' => $totalPeserta],
        ];
    }

    private function buildAttendanceSummary(string $start, string $end, ?int $sekolahId, ?int $courseId): array
    {
        $courseIds = $this->scopeCourseMeetingRange($start, $end, $sekolahId, $courseId);
        if ($courseIds->isEmpty()) {
            return ['total'=>0,'hadir'=>0,'tidak_hadir'=>0,'izin'=>0,'sakit'=>0,'total_tidak_hadir'=>0,'rate_hadir'=>0];
        }

        $rows = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('meetings as m', 'm.id', '=', 'a.meeting_id')
            ->join('courses as c', 'c.id', '=', 'm.course_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->when($sekolahId, fn($q) => $q->where('u.sekolah_id', $sekolahId))
            ->whereIn('c.id', $courseIds)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->selectRaw("
                COUNT(DISTINCT CONCAT(a.user_id,'-',a.meeting_id)) as total,
                COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir,
                COUNT(DISTINCT CASE WHEN a.status='Tidak Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as tidak_hadir,
                COUNT(DISTINCT CASE WHEN a.status='Izin' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as izin,
                COUNT(DISTINCT CASE WHEN a.status='Sakit' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as sakit
            ")
            ->first();

        $total = (int)($rows->total ?? 0);
        $hadir = (int)($rows->hadir ?? 0);
        $izin  = (int)($rows->izin ?? 0);
        $sakit = (int)($rows->sakit ?? 0);
        $tdk   = (int)($rows->tidak_hadir ?? 0);

        $totalTidak = $tdk + $izin + $sakit;
        $rateHadir = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'hadir' => $hadir,
            'tidak_hadir' => $tdk,
            'izin' => $izin,
            'sakit' => $sakit,
            'total_tidak_hadir' => $totalTidak,
            'rate_hadir' => $rateHadir,
        ];
    }

    private function buildTrendCombined(string $start, string $end, ?int $sekolahId, ?int $courseId): array
    {
        if ($courseId) {
            return $this->buildTrendByPertemuan($start, $end, $sekolahId, $courseId);
        }

        if (!$sekolahId) return [];
        return $this->buildTrendAllCoursesByDate($start, $end, $sekolahId);
    }

    private function buildTrendByPertemuan(string $start, string $end, ?int $sekolahId, int $courseId): array
    {
        $attSub = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->where('a.course_id', $courseId)
            ->whereBetween('a.tanggal', [$start, $end]);

        if ($sekolahId) $attSub->where('u.sekolah_id', $sekolahId);

        $attSub = $attSub->groupBy('a.meeting_id')
            ->selectRaw("
                a.meeting_id,
                COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir,
                COUNT(DISTINCT CASE WHEN a.status='Izin' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as izin,
                COUNT(DISTINCT CASE WHEN a.status='Sakit' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as sakit,
                COUNT(DISTINCT CASE WHEN a.status='Tidak Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as tidak_hadir
            ");

        $scoreSub = DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->whereNull('s.deleted_at')
            ->where('u.role_id', 3);

        if ($sekolahId) $scoreSub->where('u.sekolah_id', $sekolahId);

        $scoreSub = $scoreSub->groupBy('s.meeting_id')
            ->selectRaw("s.meeting_id, ROUND(AVG(s.total_score),1) as avg_score");

        $rows = DB::table('meetings as m')
            ->leftJoinSub($attSub, 'att', fn($j) => $j->on('att.meeting_id','=','m.id'))
            ->leftJoinSub($scoreSub, 'sc', fn($j) => $j->on('sc.meeting_id','=','m.id'))
            ->where('m.course_id', $courseId)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->orderBy('m.pertemuan')
            ->selectRaw("
                m.pertemuan as x,
                sc.avg_score as avg_score,
                COALESCE(att.hadir,0) as hadir,
                COALESCE(att.izin,0) as izin,
                COALESCE(att.sakit,0) as sakit,
                COALESCE(att.tidak_hadir,0) as tidak_hadir
            ")
            ->get();

        return $rows->map(function($r){
            $hadir = (int)$r->hadir; $izin=(int)$r->izin; $sakit=(int)$r->sakit; $tdk=(int)$r->tidak_hadir;
            return [
                'x' => (string)$r->x,
                'avg_score' => is_null($r->avg_score) ? null : (float)$r->avg_score,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'tidak_hadir' => $tdk,
                'total_tidak_hadir' => $tdk + $izin + $sakit,
            ];
        })->values()->all();
    }

    private function buildTrendAllCoursesByDate(string $start, string $end, int $sekolahId): array
    {
        $attRows = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->join('meetings as m', 'm.id', '=', 'a.meeting_id')
            ->join('courses as c', 'c.id', '=', 'm.course_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->where('u.sekolah_id', $sekolahId)
            ->where('c.sekolah_id', $sekolahId)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->groupBy('m.tanggal_pelaksanaan')
            ->orderBy('m.tanggal_pelaksanaan')
            ->selectRaw("
                m.tanggal_pelaksanaan as x,
                COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir,
                COUNT(DISTINCT CASE WHEN a.status='Izin' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as izin,
                COUNT(DISTINCT CASE WHEN a.status='Sakit' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as sakit,
                COUNT(DISTINCT CASE WHEN a.status='Tidak Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as tidak_hadir
            ")
            ->get()
            ->keyBy('x');

        $scoreRows = DB::table('scores as s')
            ->join('meetings as m', 'm.id', '=', 's.meeting_id')
            ->join('courses as c', 'c.id', '=', 'm.course_id')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->whereNull('s.deleted_at')
            ->where('u.role_id', 3)
            ->where('u.sekolah_id', $sekolahId)
            ->where('c.sekolah_id', $sekolahId)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->groupBy('m.tanggal_pelaksanaan')
            ->orderBy('m.tanggal_pelaksanaan')
            ->selectRaw("m.tanggal_pelaksanaan as x, ROUND(AVG(s.total_score),1) as avg_score")
            ->get()
            ->keyBy('x');

        $allDates = $attRows->keys()->merge($scoreRows->keys())->unique()->sort()->values();

        $out = [];
        foreach ($allDates as $d) {
            $att = $attRows->get($d);
            $sc  = $scoreRows->get($d);

            $hadir = (int)($att->hadir ?? 0);
            $izin  = (int)($att->izin ?? 0);
            $sakit = (int)($att->sakit ?? 0);
            $tdk   = (int)($att->tidak_hadir ?? 0);

            $out[] = [
                'x' => $d,
                'avg_score' => isset($sc->avg_score) ? (float)$sc->avg_score : null,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'tidak_hadir' => $tdk,
                'total_tidak_hadir' => $tdk + $izin + $sakit,
            ];
        }

        return $out;
    }

    private function buildCourseSummaryRows(string $start, string $end, int $sekolahId): array
    {
        $courses = DB::table('courses as c')
            ->whereNull('c.deleted_at')
            ->where('c.sekolah_id', $sekolahId)
            ->select('c.id', 'c.nama_kelas')
            ->orderBy('c.nama_kelas')
            ->get();

        $out = [];

        foreach ($courses as $c) {
            $meetingAgg = DB::table('meetings')
                ->where('course_id', $c->id)
                ->whereBetween('tanggal_pelaksanaan', [$start, $end])
                ->selectRaw("COUNT(*) as cnt, MIN(tanggal_pelaksanaan) as min_date, MAX(tanggal_pelaksanaan) as max_date")
                ->first();

            $jumlahPertemuan = (int)($meetingAgg->cnt ?? 0);
            if ($jumlahPertemuan === 0) continue;

            $avgScore = DB::table('scores as s')
                ->join('meetings as m', 'm.id', '=', 's.meeting_id')
                ->join('users as u', 'u.id', '=', 's.peserta_id')
                ->whereNull('s.deleted_at')
                ->where('u.role_id', 3)
                ->where('u.sekolah_id', $sekolahId)
                ->where('m.course_id', $c->id)
                ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
                ->selectRaw("ROUND(AVG(s.total_score),1) as avg_score")
                ->value('avg_score');

            $att = DB::table('attendances as a')
                ->join('users as u', 'u.id', '=', 'a.user_id')
                ->join('meetings as m', 'm.id', '=', 'a.meeting_id')
                ->whereNull('a.deleted_at')
                ->where('u.role_id', 3)
                ->where('u.sekolah_id', $sekolahId)
                ->where('m.course_id', $c->id)
                ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
                ->selectRaw("
                    COUNT(DISTINCT CONCAT(a.user_id,'-',a.meeting_id)) as total,
                    COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir
                ")
                ->first();

            $total = (int)($att->total ?? 0);
            $hadir = (int)($att->hadir ?? 0);
            $pct = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

            $out[] = [
                'nama_kelas' => $c->nama_kelas,
                'jumlah_pertemuan' => $jumlahPertemuan,
                'tgl_mulai' => $meetingAgg->min_date,
                'tgl_akhir' => $meetingAgg->max_date,
                'avg_score' => $avgScore ?? 0,
                'pct_hadir' => $pct,
            ];
        }

        return $out;
    }

    private function buildParticipantsTable(Request $request, string $start, string $end, ?int $sekolahId, ?int $courseId)
    {
        $perPage = 10;
        $q = $this->participantsQueryBase($start, $end, $sekolahId, $courseId);
        return $q->paginate($perPage)->appends($request->query());
    }

    private function buildParticipantsAll(string $start, string $end, ?int $sekolahId, ?int $courseId)
    {
        $q = $this->participantsQueryBase($start, $end, $sekolahId, $courseId);
        return $q->get();
    }

    private function participantsQueryBase(string $start, string $end, ?int $sekolahId, ?int $courseId)
    {
        // score avg per peserta (scope)
        $scoreAgg = DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->whereNull('s.deleted_at')
            ->where('u.role_id', 3)
            ->selectRaw("s.peserta_id as peserta_id, ROUND(AVG(s.total_score),1) as avg_score")
            ->groupBy('s.peserta_id');

        if ($sekolahId) $scoreAgg->where('u.sekolah_id', $sekolahId);

        if ($courseId) {
            $scoreAgg->join('meetings as m', 'm.id', '=', 's.meeting_id')
                ->where('m.course_id', $courseId)
                ->whereBetween('m.tanggal_pelaksanaan', [$start, $end]);
        } else {
            if ($sekolahId) {
                $scoreAgg->join('meetings as m', 'm.id', '=', 's.meeting_id')
                    ->join('courses as c', 'c.id', '=', 'm.course_id')
                    ->where('c.sekolah_id', $sekolahId)
                    ->whereBetween('m.tanggal_pelaksanaan', [$start, $end]);
            } else {
                return DB::table('users')->whereRaw('1=0'); // safety
            }
        }

        $base = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'u.kelas_id')
            ->leftJoin('courses as c', 'c.id', '=', 'a.course_id')
            ->whereNull('a.deleted_at')
            ->where('u.role_id', 3)
            ->whereBetween('a.tanggal', [$start, $end]);

        if ($sekolahId) $base->where('u.sekolah_id', $sekolahId);
        if ($courseId)  $base->where('a.course_id', $courseId);

        $base->leftJoinSub($scoreAgg, 'sc', function($j){
            $j->on('sc.peserta_id','=','u.id');
        });

        return $base->selectRaw("
            u.id as user_id,
            u.name,
            k.nama as kelas_name,
            c.nama_kelas as course_name,
            COUNT(DISTINCT CASE WHEN a.status='Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as hadir,
            COUNT(DISTINCT CASE WHEN a.status='Tidak Hadir' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as tidak_hadir,
            COUNT(DISTINCT CASE WHEN a.status='Izin' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as izin,
            COUNT(DISTINCT CASE WHEN a.status='Sakit' THEN CONCAT(a.user_id,'-',a.meeting_id) END) as sakit,
            COALESCE(sc.avg_score, 0) as avg_score
        ")
        ->groupBy('u.id', 'u.name', 'k.nama', 'c.nama_kelas', 'sc.avg_score')
        ->orderByDesc('avg_score');
    }

    public function boot()
    {
        Paginator::useBootstrapFive(); // Or useBootstrap() if needed

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }
}
