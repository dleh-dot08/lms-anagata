<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\Score;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseScoresExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function view(): View
    {
        return view('exports.course-scores', [
            'course' => $this->course->load(['enrollments.user.sekolah.jenjang', 'meetings', 'mentor', 'kategori', 'program', 'sekolah'])
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Style for the header rows
        $sheet->getStyle('A1:Z2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0',
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add borders to all cells
        $sheet->getStyle($sheet->calculateWorksheetDimension())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 