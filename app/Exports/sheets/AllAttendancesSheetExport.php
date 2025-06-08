<?php

namespace App\Exports\Sheets;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // <-- Import WithStyles
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // <-- Import Worksheet

class AllAttendancesSheetExport implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        $query = Attendance::with(['user.kelas', 'user.sekolah', 'user.jenjang', 'course', 'activity', 'recordedByMentor']);

        if ($this->startDate) {
            $query->whereDate('tanggal', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('tanggal', '<=', $this->endDate);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Role',
            'Kursus / Kegiatan',
            'Sekolah',
            'Kelas',
            'Jenjang',
            'Status',
            'Tanggal Absen',
            'Waktu Absen',
            'Pengabsen',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->name ?? 'N/A',
            $attendance->user->role_id == 2 ? 'Mentor' : ($attendance->user->role_id == 3 ? 'Peserta' : 'Lainnya'),
            $attendance->course->nama_kelas ?? ($attendance->activity->nama_kegiatan ?? 'N/A'),
            $attendance->user->sekolah->nama_sekolah ?? 'N/A',
            $attendance->user->kelas->nama_kelas ?? 'N/A',
            $attendance->user->jenjang->nama_jenjang ?? 'N/A',
            $attendance->status,
            \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y'),
            \Carbon\Carbon::parse($attendance->created_at)->format('H:i:s'),
            $attendance->recordedByMentor->name ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return 'Semua Absensi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Gaya untuk baris pertama (header)
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'A4CCD9'], // Warna abu-abu terang
                ],
            ],
        ];
    }
}