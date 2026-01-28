<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfografisController extends Controller
{
    public function index(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id;
        $courseId  = $request->integer('course_id');

        // Sekolah hanya boleh semester aktif
        $activeSemester = DB::table('semesters')->where('is_active', 1)->first();

        $courses = collect();
        $cards = [];
        $kehadiran = null;
        $summaryText = null;

        if ($activeSemester && $activeSemester->start_date && $activeSemester->end_date) {
            $courses = $this->getCoursesBySemesterRange($activeSemester->start_date, $activeSemester->end_date, $sekolahId);
        }

        if ($activeSemester && $courseId) {
            [$start, $end] = [$activeSemester->start_date, $activeSemester->end_date];
            $cards = $this->buildRingkasanCards($courseId, $start, $end, $sekolahId);
            $kehadiran = $this->buildKehadiranSummary($courseId, $start, $end, $sekolahId);
            $summaryText = $this->getApprovedSummaryText($activeSemester->id, $courseId);
        }

        return view('sekolah.infografis.index', compact(
            'activeSemester',
            'courseId',
            'courses',
            'cards',
            'kehadiran',
            'summaryText'
        ));
    }

    public function export(Request $request)
    {
        return back()->with('error', 'Fitur Export PDF belum diaktifkan (butuh library PDF).');
    }

    private function getCoursesBySemesterRange(string $start, string $end, int $sekolahId)
    {
        return DB::table('courses as c')
            ->join('meetings as m', 'm.course_id', '=', 'c.id')
            ->whereNull('c.deleted_at')
            ->where('c.sekolah_id', $sekolahId)
            ->whereBetween('m.tanggal_pelaksanaan', [$start, $end])
            ->select('c.id', 'c.nama_kelas')
            ->groupBy('c.id', 'c.nama_kelas')
            ->orderBy('c.nama_kelas')
            ->get();
    }

    private function buildRingkasanCards(int $courseId, string $start, string $end, int $sekolahId): array
    {
        $totalKelas = DB::table('meetings')
            ->where('course_id', $courseId)
            ->whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->count();

        $totalSiswaQ = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.course_id', $courseId)
            ->whereNull('a.deleted_at')
            ->whereBetween('a.tanggal', [$start, $end])
            ->where('u.role_id', 3)
            ->where('u.sekolah_id', $sekolahId);

        $totalSiswa = (clone $totalSiswaQ)->distinct('a.user_id')->count('a.user_id');
        $siswaAktif = $totalSiswa;

        return [
            ['label' => 'Total Kelas', 'value' => $totalKelas],
            ['label' => 'Total Siswa', 'value' => $totalSiswa],
            ['label' => 'Siswa Aktif', 'value' => $siswaAktif],
        ];
    }

    private function buildKehadiranSummary(int $courseId, string $start, string $end, int $sekolahId): array
    {
        $row = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.course_id', $courseId)
            ->whereNull('a.deleted_at')
            ->whereBetween('a.tanggal', [$start, $end])
            ->where('u.role_id', 3)
            ->where('u.sekolah_id', $sekolahId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN LOWER(COALESCE(a.status,''))='hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN LOWER(COALESCE(a.status,''))='izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN LOWER(COALESCE(a.status,''))='sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN LOWER(COALESCE(a.status,''))='alfa' THEN 1 ELSE 0 END) as alfa
            ")->first();

        $total = (int)($row->total ?? 0);
        $hadir = (int)($row->hadir ?? 0);
        $rate = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

        $mentorHadir = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.course_id', $courseId)
            ->whereNull('a.deleted_at')
            ->whereBetween('a.tanggal', [$start, $end])
            ->where('u.sekolah_id', $sekolahId)
            ->whereNotNull('a.recorded_by_user_id')
            ->where(function ($qq) {
                $qq->whereNotNull('a.longitude')->orWhereNotNull('a.latitude');
            })
            ->distinct(DB::raw("CONCAT(a.recorded_by_user_id,'|',a.tanggal)"))
            ->count();

        return [
            'total' => $total,
            'hadir' => (int)($row->hadir ?? 0),
            'izin'  => (int)($row->izin ?? 0),
            'sakit' => (int)($row->sakit ?? 0),
            'alfa'  => (int)($row->alfa ?? 0),
            'rate_hadir' => $rate,
            'mentor_hadir' => $mentorHadir,
        ];
    }

    private function getApprovedSummaryText(int $semesterId, int $courseId): ?string
    {
        $row = DB::table('course_summaries')
            ->where('semester_id', $semesterId)
            ->where('course_id', $courseId)
            ->first();

        return $row?->summary_text;
    }
}
