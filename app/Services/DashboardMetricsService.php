<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * ADMIN: global metrics
     */
    public function admin(): array
    {
        $now = Carbon::now();
        $from7  = $now->copy()->subDays(7);
        $from30 = $now->copy()->subDays(30);

        // Sesuaikan nama tabel jika berbeda di DB Anda
        $totalUsers    = DB::table('users')->count();
        $totalPeserta  = DB::table('users')->where('role_id', 3)->count(); // asumsi peserta role_id=3
        $totalSekolah  = DB::table('sekolah')->count(); // jika tabel Anda namanya "sekolah"
        $totalKelas    = DB::table('kelas')->count();   // jika ada tabel "kelas"

        // Definisi "aktif" versi aman: peserta yang punya aktivitas (contoh: submissions) 30 hari terakhir
        // Kalau Anda belum punya tabel submissions/aktivitas, ganti ke updated_at atau last_login_at (kalau ada)
        $pesertaAktif30 = $this->countActiveUsersByActivityTable('assignment_submissions', 'user_id', $from30);

        // Tren 14 hari terakhir (contoh: pendaftaran peserta baru)
        $trendPeserta = DB::table('users')
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->where('role_id', 3)
            ->where('created_at', '>=', $now->copy()->subDays(14))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return [
            'cards' => [
                ['label' => 'Total Pengguna', 'value' => $totalUsers],
                ['label' => 'Total Peserta', 'value' => $totalPeserta],
                ['label' => 'Total Sekolah', 'value' => $totalSekolah],
                ['label' => 'Total Kelas', 'value' => $totalKelas],
                ['label' => 'Peserta Aktif (30 Hari)', 'value' => $pesertaAktif30],
            ],
            'trendPeserta' => $trendPeserta,
        ];
    }

    /**
     * SEKOLAH: metrics terfilter sekolah
     */
    public function sekolah(int $sekolahId): array
    {
        $now = Carbon::now();
        $from30 = $now->copy()->subDays(30);
        $from14 = $now->copy()->subDays(14);

        // ===== KPI: Total peserta sekolah =====
        $totalPeserta = DB::table('users')
            ->where('role_id', 3) // peserta
            ->where('sekolah_id', $sekolahId)
            ->count();

        // ===== KPI: Peserta aktif 30 hari (proxy: ada absensi atau nilai 30 hari) =====
        // 1) aktif dari attendances
        $aktifByAttendance = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('a.deleted_at')
            ->where('a.created_at', '>=', $from30)
            ->distinct('a.user_id')
            ->count('a.user_id');

        // 2) aktif dari scores
        $aktifByScores = DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('s.deleted_at')
            ->where('s.created_at', '>=', $from30)
            ->distinct('s.peserta_id')
            ->count('s.peserta_id');

        $pesertaAktif30 = max($aktifByAttendance, $aktifByScores);

        // ===== KPI: Rata-rata total score 30 hari =====
        $avgTotalScore30 = (float) DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('s.deleted_at')
            ->where('s.created_at', '>=', $from30)
            ->avg('s.total_score');

        // ===== KPI: Kehadiran peserta 14 hari (rate) =====
        // NOTE: Karena value status belum kamu tulis, aku pakai normalisasi: hadir jika status mengandung "hadir/present"
        $attendanceAgg14 = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('a.deleted_at')
            ->where('a.tanggal', '>=', $from14->toDateString())
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE
                    WHEN LOWER(COALESCE(a.status,'')) LIKE '%hadir%'
                      OR LOWER(COALESCE(a.status,'')) LIKE '%present%'
                    THEN 1 ELSE 0 END
                ) as hadir
            ")
            ->first();

        $totalAttendance14 = (int) ($attendanceAgg14->total ?? 0);
        $hadirAttendance14 = (int) ($attendanceAgg14->hadir ?? 0);
        $rateHadir14 = $totalAttendance14 > 0 ? round(($hadirAttendance14 / $totalAttendance14) * 100, 1) : 0;

        // ===== KPI: Kehadiran mentor (proxy) =====
        // Karena absensi dicatat mentor, kolom recorded_by_user_id mengindikasikan siapa yang mencatat.
        // "Kehadiran mentor di kelas" = absensi yang dicatat mentor DAN lokasi terisi.
        $mentorHadirKelas14 = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('a.deleted_at')
            ->where('a.tanggal', '>=', $from14->toDateString())
            ->whereNotNull('a.recorded_by_user_id')
            ->where(function ($q) {
                $q->whereNotNull('a.longitude')
                  ->orWhereNotNull('a.latitude');
            })
            ->distinct(DB::raw("CONCAT(a.recorded_by_user_id,'|',a.tanggal)"))
            ->count();

        // ===== nilaiSeries: rata-rata per course (top 8) =====
        // Join: scores -> meetings -> course? Kita belum punya tabel meetings/courses mapping yang pasti.
        // Minimal yang aman: rata-rata per mentor (atau per meeting).
        // Aku buat default: rata-rata per mentor (top 8). Kalau kamu punya relasi meeting->course, nanti kita upgrade ke per course.
        $nilaiSeries = DB::table('scores as s')
            ->join('users as u', 'u.id', '=', 's.peserta_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('s.deleted_at')
            ->where('s.created_at', '>=', $from30)
            ->selectRaw("s.mentor_id as key_id, ROUND(AVG(s.total_score),1) as avg_score")
            ->groupBy('s.mentor_id')
            ->orderByDesc('avg_score')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                return [
                    'label' => 'Mentor #' . ($row->key_id ?? '-'),
                    'value' => (float) ($row->avg_score ?? 0),
                ];
            })
            ->values()
            ->all();

        // ===== absensiTrend: 14 hari hadir vs tidak hadir =====
        $absensiTrend = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('a.deleted_at')
            ->where('a.tanggal', '>=', $from14->toDateString())
            ->selectRaw("
                a.tanggal as d,
                SUM(CASE
                    WHEN LOWER(COALESCE(a.status,'')) LIKE '%hadir%'
                      OR LOWER(COALESCE(a.status,'')) LIKE '%present%'
                    THEN 1 ELSE 0 END) as hadir,
                SUM(CASE
                    WHEN LOWER(COALESCE(a.status,'')) LIKE '%hadir%'
                      OR LOWER(COALESCE(a.status,'')) LIKE '%present%'
                    THEN 0 ELSE 1 END) as alfa
            ")
            ->groupBy('a.tanggal')
            ->orderBy('a.tanggal')
            ->get()
            ->map(fn($r) => [
                'd' => (string) $r->d,
                'hadir' => (int) ($r->hadir ?? 0),
                'alfa'  => (int) ($r->alfa ?? 0),
            ])
            ->values()
            ->all();

        // ===== mentorNotes: catatan mentor terbaru =====
        // NOTE: mentor_notes tidak ada peserta_id, jadi ini catatan per meeting/pertemuan.
        $mentorNotes = DB::table('mentor_notes')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get()
            ->map(function ($n) {
                return [
                    'student_name' => 'Catatan Pertemuan (Meeting #' . ($n->meeting_id ?? '-') . ')',
                    'kelas' => null,
                    'mentor_name' => null,
                    'date' => $n->created_at ? Carbon::parse($n->created_at)->format('d M Y H:i') : null,
                    'note' => $this->compactNote($n),
                    'tag'  => $n->materi ? 'Materi: ' . $n->materi : null,
                ];
            })
            ->values()
            ->all();

        // ===== topStudents: peserta paling aktif berdasarkan jumlah absensi 30 hari =====
        $topStudents = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('kelas as k', 'k.id', '=', 'u.kelas_id')
            ->where('u.sekolah_id', $sekolahId)
            ->where('u.role_id', 3)
            ->whereNull('a.deleted_at')
            ->where('a.created_at', '>=', $from30)
            ->selectRaw("u.id, u.name, k.nama as kelas, COUNT(*) as c")
            ->groupBy('u.id', 'u.name', 'k.nama')
            ->orderByDesc('c')
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'name' => $r->name,
                'kelas' => $r->kelas,
                'count' => (int) $r->c,
            ])
            ->values()
            ->all();

        // ===== Cards untuk Blade =====
        $cards = [
            ['label' => 'Total Peserta', 'value' => $totalPeserta, 'hint' => 'Semua peserta terdaftar di sekolah ini'],
            ['label' => 'Peserta Aktif (30 Hari)', 'value' => $pesertaAktif30, 'hint' => 'Aktif via absensi / nilai'],
            ['label' => 'Rata-rata Nilai (30 Hari)', 'value' => round($avgTotalScore30, 1), 'hint' => 'Avg total_score'],
            ['label' => 'Kehadiran Peserta (14 Hari)', 'value' => $rateHadir14 . '%', 'hint' => 'Persentase hadir'],
            ['label' => 'Mentor Hadir di Kelas (14 Hari)', 'value' => $mentorHadirKelas14, 'hint' => 'Lokasi terisi + dicatat mentor'],
        ];

        return compact('cards', 'nilaiSeries', 'absensiTrend', 'mentorNotes', 'topStudents');
    }

    /**
     * PESERTA: metrics terfilter user
     */
    public function peserta(int $userId): array
    {
        $now = Carbon::now();
        $from7 = $now->copy()->subDays(7);

        // Contoh: aktivitas submission 7 hari
        $submission7 = DB::table('assignment_submissions')
            ->where('user_id', $userId)
            ->where('created_at', '>=', $from7)
            ->count();

        // Contoh: rapor tersedia (sesuaikan jika tabel rapor beda)
        $eraportCount = $this->safeCount('eraport_entries', ['user_id' => $userId]);

        return [
            'cards' => [
                ['label' => 'Aktivitas 7 Hari', 'value' => $submission7],
                ['label' => 'E-Raport Tersedia', 'value' => $eraportCount],
            ],
        ];
    }

    /**
     * Helper: kalau tabel aktivitas belum ada, jangan bikin app error.
     */
    private function countActiveUsersByActivityTable(string $table, string $userIdCol, Carbon $from): int
    {
        if (!$this->tableExists($table)) return 0;

        return DB::table($table)
            ->where('created_at', '>=', $from)
            ->distinct($userIdCol)
            ->count($userIdCol);
    }

    private function compactNote($n): string
    {
        $parts = [];

        if (!empty($n->project)) $parts[] = "Project: {$n->project}";
        if (!empty($n->sikap_siswa)) $parts[] = "Sikap: {$n->sikap_siswa}";
        if (!empty($n->hambatan)) $parts[] = "Hambatan: {$n->hambatan}";
        if (!empty($n->solusi)) $parts[] = "Solusi: {$n->solusi}";
        if (!empty($n->masukan)) $parts[] = "Masukan: {$n->masukan}";
        if (!empty($n->lain_lain)) $parts[] = "Lain-lain: {$n->lain_lain}";

        if (count($parts) === 0) return '-';
        return implode(" • ", $parts);
    }

    private function safeCount(string $table, array $where = []): int
    {
        if (!$this->tableExists($table)) return 0;

        $q = DB::table($table);
        foreach ($where as $k => $v) $q->where($k, $v);
        return $q->count();
    }

    private function tableExists(string $table): bool
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
