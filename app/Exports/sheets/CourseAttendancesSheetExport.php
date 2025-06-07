<?php

namespace App\Exports\Sheets;

use App\Models\Attendance;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles; // <-- Import WithStyles
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // <-- Import Worksheet

class CourseAttendancesSheetExport implements FromQuery, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $courseId;
    protected $startDate;
    protected $endDate;
    protected $courseName;

    public function __construct(int $courseId, $startDate = null, $endDate = null)
    {
        $this->courseId = $courseId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->courseName = Course::find($courseId)->nama_kelas ?? 'Unknown Course';
    }

    public function query()
    {
        $query = Attendance::with(['user', 'user.sekolah', 'user.jenjang', 'recordedByMentor'])
                            ->where('course_id', $this->courseId);

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
            'Sekolah',
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
            $attendance->user->sekolah->nama_sekolah ?? 'N/A',
            $attendance->user->jenjang->nama_jenjang ?? 'N/A',
            $attendance->status,
            \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y'),
            \Carbon\Carbon::parse($attendance->created_at)->format('H:i:s'),
            $attendance->recordedByMentor->name ?? 'N/A',
        ];
    }

    public function title(): string
    {
        return $this->courseName;
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Gaya untuk baris pertama (header)
            1    => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'A4CCD9'], 
                ],
            ],
        ];
    }
}