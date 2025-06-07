<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Course; // Diperlukan untuk mendapatkan nama kursus
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable; // Opsional: Untuk use $this->download()

class SchoolCourseAttendanceExport implements WithMultipleSheets
{
    use Exportable; // Bisa dipakai kalau mau pakai ->download() di sini

    protected $attendances;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Dapatkan semua ID kursus unik dari koleksi absensi yang sudah difilter
        // Hanya ambil absensi yang bertipe 'kursus'
        $courseIds = $this->attendances
                            ->whereNotNull('course_id')
                            ->pluck('course_id')
                            ->unique();

        // Jika tidak ada filter course_id, dan ada absensi kegiatan, tambahkan sheet umum untuk kegiatan
        $activityAttendances = $this->attendances->whereNotNull('activity_id');
        if ($activityAttendances->isNotEmpty()) {
            $sheets[] = new AttendanceSheet($activityAttendances, 'Absensi Kegiatan (Semua)');
        }


        // Untuk setiap kursus, buat dua sheet (Mentor & Siswa)
        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId); // Ambil objek Course untuk namanya

            if ($course) {
                // Filter absensi untuk kursus ini
                $courseAttendances = $this->attendances->where('course_id', $courseId);

                // Absensi Mentor untuk kursus ini
                $mentorAttendances = $courseAttendances->filter(function ($attendance) {
                    return $attendance->user && $attendance->user->role_id == 2; // Asumsi role_id 2 untuk Mentor
                });
                if ($mentorAttendances->isNotEmpty()) {
                    $sheets[] = new AttendanceSheet($mentorAttendances, "{$course->nama_kelas} - Mentor");
                }

                // Absensi Siswa untuk kursus ini
                $participantAttendances = $courseAttendances->filter(function ($attendance) {
                    return $attendance->user && $attendance->user->role_id == 3; // Asumsi role_id 3 untuk Siswa/Peserta
                });
                if ($participantAttendances->isNotEmpty()) {
                    $sheets[] = new AttendanceSheet($participantAttendances, "{$course->nama_kelas} - Siswa");
                }
            }
        }

        // Jika tidak ada absensi kursus sama sekali, atau semua absensi adalah kegiatan,
        // pastikan setidaknya ada satu sheet jika ada data
        if (empty($sheets) && $this->attendances->isNotEmpty()) {
            $sheets[] = new AttendanceSheet($this->attendances, 'Absensi Lainnya');
        }

        return $sheets;
    }
}