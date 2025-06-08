<?php

namespace App\Exports\Sheets;

use App\Models\Attendance;
use App\Models\Sekolah;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // <-- Import WithStyles
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // <-- Import Worksheet

class SchoolAttendancesSheetExport implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $schoolId;
    protected $startDate;
    protected $endDate;
    protected $schoolName;

    public function __construct(int $schoolId, $startDate = null, $endDate = null)
    {
        $this->schoolId = $schoolId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->schoolName = Sekolah::find($schoolId)->nama_sekolah ?? 'Unknown School';
    }

    public function query()
    {
        $query = Attendance::with(['user', 'user.kelas', 'user.jenjang', 'recordedByMentor'])
                            ->whereHas('user', fn($q) => $q->where('sekolah_id', $this->schoolId));

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
            'Nama Peserta',
            'Role',
            'Kelas',
            'Jenjang',
            'Status Absensi',
            'Tanggal',
            'Waktu Absen',
            'Pengabsen',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->name ?? 'N/A',
            $attendance->user->role_id == 2 ? 'Mentor' : ($attendance->user->role_id == 3 ? 'Peserta' : 'Lainnya'),
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
        return $this->schoolName;
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