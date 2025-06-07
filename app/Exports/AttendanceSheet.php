<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class AttendanceSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $attendances;
    protected $title;
    protected $rowNumber = 0; // Tambahkan properti untuk menyimpan nomor baris

    public function __construct(Collection $attendances, string $title)
    {
        $this->attendances = $attendances;
        $this->title = $title;
    }

    public function collection()
    {
        // Reset nomor baris setiap kali koleksi baru diproses (untuk sheet baru)
        $this->rowNumber = 0;
        return $this->attendances;
    }

    public function headings(): array
    {
        return [
            'Nomor', // Diubah dari 'ID Absensi'
            'Nama',
            'Email',
            'Role',
            'Nama Kelas/Kegiatan',
            'Jenis Absensi',
            'Status Kehadiran',
            'Tanggal Absen',
            'Waktu Absen Dibuat',
            'Sekolah',
            'Kelas Siswa',
            'Jenjang',
        ];
    }

    public function map($attendance): array
    {
        $this->rowNumber++;

        $courseOrActivityName = '';
        if ($attendance->course) {
            $courseOrActivityName = $attendance->course->nama_kelas;
        } elseif ($attendance->activity) {
            $courseOrActivityName = $attendance->activity->nama_kegiatan;
        }

        $attendanceType = '';
        if ($attendance->course_id) {
            $attendanceType = 'Kursus';
        } elseif ($attendance->activity_id) {
            $attendanceType = 'Kegiatan';
        }

        // Pastikan relasi school, kelas, dan jenjang dimuat di user
        $schoolName = $attendance->user->school->nama_sekolah ?? 'N/A';
        $className = $attendance->user->kelas->nama_kelas ?? 'N/A';
        $levelName = $attendance->user->jenjang->nama_jenjang ?? ($attendance->course->level->nama_jenjang ?? 'N/A'); // Cek di user atau di course

        return [
            $this->rowNumber, // Menggunakan nomor baris sebagai kolom pertama
            $attendance->user->name ?? 'N/A',
            $attendance->user->email ?? 'N/A',
            $attendance->user->role->name ?? 'N/A',
            $courseOrActivityName,
            $attendanceType,
            $attendance->status,
            Carbon::parse($attendance->tanggal)->format('d M Y'),
            Carbon::parse($attendance->created_at)->format('d M Y H:i:s'),
            $schoolName,
            $className,
            $levelName,
        ];
    }

    public function title(): string
    {
        // Pastikan nama sheet tidak lebih dari 31 karakter dan tidak mengandung karakter ilegal
        $cleanedTitle = str_replace([':', '/', '\\', '?', '*', '[', ']'], '', $this->title);
        return substr($cleanedTitle, 0, 31); // Batasi 31 karakter
    }
}