<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Score;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function nilaiIndex(Request $request)
    {
        $query = Course::with(['sekolah', 'kategori', 'mentor']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('sekolah', function($q) use ($search) {
                      $q->where('nama_sekolah', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by sekolah
        if ($request->has('sekolah_id') && $request->sekolah_id != '') {
            $query->where('sekolah_id', $request->sekolah_id);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);
        $sekolah = Sekolah::orderBy('nama_sekolah')->get();

        return view('admin.reports.nilai.index', compact('courses', 'sekolah'));
    }

    public function show($id)
    {
        $course = Course::with([
            'participants.sekolah.jenjang',
            'meetings',
            // Eager load scores. Di dalam scores, kita filter melalui relasi 'meeting'
            'participants.scores' => function ($query) use ($id) {
                $query->whereHas('meeting', function ($q) use ($id) {
                    $q->where('course_id', $id);
                })->with('mentor'); // Tetap eager load mentor
            }
        ])->findOrFail($id);

        return view('admin.reports.nilai.show', compact('course'));
    }

    public function edit($id)
    {
        $course = Course::with(['participants.sekolah.jenjang', 'meetings'])
            ->findOrFail($id);

        return view('admin.reports.nilai.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $currentUser = Auth::user();

        foreach ($request->scores as $pesertaId => $meetingScores) {
            foreach ($meetingScores as $meetingId => $scores) {
                // Calculate total score
                $total = 0;
                $count = 0;
                if (isset($scores['creativity_score']) && $scores['creativity_score'] !== '') {
                    $total += floatval($scores['creativity_score']);
                    $count++;
                }
                if (isset($scores['program_score']) && $scores['program_score'] !== '') {
                    $total += floatval($scores['program_score']);
                    $count++;
                }
                if (isset($scores['design_score']) && $scores['design_score'] !== '') {
                    $total += floatval($scores['design_score']);
                    $count++;
                }

                $scores['total_score'] = $count > 0 ? $total / $count : null;
                $scores['mentor_id'] = $currentUser->id;

                Score::updateOrCreate(
                    [
                        'meeting_id' => $meetingId,
                        'peserta_id' => $pesertaId,
                    ],
                    $scores
                );
            }
        }

        return redirect()
            ->route('admin.reports.nilai.show', $course->id)
            ->with('success', 'Nilai berhasil disimpan');
    }

    public function exportNilai($id)
    {
        $course = Course::with(['participants.kelas', 'meetings', 'mentor', 'kategori', 'program', 'sekolah'])
            ->findOrFail($id);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Course Details
        $sheet->setCellValue('A1', 'Detail Kursus');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        
        $sheet->setCellValue('A2', 'Nama Kelas');
        $sheet->setCellValue('B2', $course->nama_kelas);
        $sheet->mergeCells('B2:C2');
        
        $sheet->setCellValue('A3', 'Kode Unik');
        $sheet->setCellValue('B3', $course->kode_unik);
        $sheet->mergeCells('B3:C3');
        
        $sheet->setCellValue('A4', 'Mentor');
        $sheet->setCellValue('B4', $course->mentor->name ?? 'Tidak Ada');
        $sheet->mergeCells('B4:C4');
        
        $sheet->setCellValue('A5', 'Sekolah');
        $sheet->setCellValue('B5', $course->sekolah->nama_sekolah ?? 'Tidak Ada');
        $sheet->mergeCells('B5:C5');
        
        $sheet->setCellValue('A6', 'Kelas');
        $sheet->setCellValue('B6', $course->kelas->nama_kelas ?? 'Tidak Ada');
        $sheet->mergeCells('B6:C6');

        $sheet->setCellValue('A7', 'Program');
        $sheet->setCellValue('B7', $course->program->nama_program ?? 'Tidak Ada');
        $sheet->mergeCells('B7:C7');
        
        $sheet->setCellValue('A8', 'Kategori');
        $sheet->setCellValue('B8', $course->kategori->nama_kategori ?? 'Tidak Ada');
        $sheet->mergeCells('B8:C8');

        // Style for course details
        $sheet->getStyle('A1:C8')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
        $sheet->getStyle('A2:A8')->getFont()->setBold(true);

        // Headers for scores table - start at row 9
        $row = 10;
        $sheet->setCellValue('A'.$row, 'No');
        $sheet->setCellValue('B'.$row, 'Nama Siswa');
        $sheet->setCellValue('C'.$row, 'Kelas');

        // Merge header cells vertically
        $sheet->mergeCells('A10:A12');
        $sheet->mergeCells('B10:B12');
        $sheet->mergeCells('C10:C12');

        // Set vertical alignment for merged cells
        $sheet->getStyle('A10:C12')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Column tracking
        $col = 'D';
        foreach ($course->meetings as $meeting) {
            // Merge cells for "Pertemuan X" header
            $startCol = $col;
            for ($i = 0; $i < 3; $i++) { // Skip one column for next meeting
                $col++;
            }
            $sheet->mergeCells($startCol.$row.':'.$col.$row);
            $sheet->setCellValue($startCol.$row, 'Pertemuan '.$meeting->pertemuan);
            
            // Add tanggal_pelaksanaan in the row below
            $dateRow = $row + 1;
            $sheet->mergeCells($startCol.$dateRow.':'.$col.$dateRow);
            $sheet->setCellValue($startCol.$dateRow, $meeting->tanggal_pelaksanaan ? \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->format('d/m/Y') : '-');
            $sheet->getStyle($startCol.$dateRow.':'.$col.$dateRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($startCol.$dateRow.':'.$col.$dateRow)->getFont()->setSize(9);
            $col++;
        }

        // Sub-headers for scores
        $row = $row + 2; // Move down two rows to accommodate the dates
        $col = 'D';
        foreach ($course->meetings as $meeting) {
            $sheet->setCellValue($col.$row, 'Creativity');
            $col++;
            $sheet->setCellValue($col.$row, 'Program');
            $col++;
            $sheet->setCellValue($col.$row, 'Design');
            $col++;
            $sheet->setCellValue($col.$row, 'Total');
            $col++;
        }

        // Style for headers
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A10:'.$col.($row))->applyFromArray($headerStyle);

        // Data rows
        $row++;
        foreach ($course->participants as $index => $participant) {
            $dataRow = $row + $index;
            $sheet->setCellValue('A'.$dataRow, $index + 1);
            $sheet->setCellValue('B'.$dataRow, $participant->name);
            $sheet->setCellValue('C'.$dataRow, $participant->kelas ? $participant->kelas->nama_kelas : '-');

            $col = 'D';
            foreach ($course->meetings as $meeting) {
                $score = Score::where('peserta_id', $participant->id)
                    ->where('meeting_id', $meeting->id)
                    ->first();

                $sheet->setCellValue($col.$dataRow, ($score && $score->creativity_score !== null) ? number_format($score->creativity_score, 1) : '-');
                $col++;
                $sheet->setCellValue($col.$dataRow, ($score && $score->program_score !== null) ? number_format($score->program_score, 1) : '-');
                $col++;
                $sheet->setCellValue($col.$dataRow, ($score && $score->design_score !== null) ? number_format($score->design_score, 1) : '-');
                $col++;
                $sheet->setCellValue($col.$dataRow, ($score && $score->total_score !== null) ? number_format($score->total_score, 1) : '-');
                $col++;
            }
        }

        // Style for data rows
        $lastRow = $row + count($course->participants) - 1;
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A'.$row.':'.$col.$lastRow)->applyFromArray($dataStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        
        // Fix for column dimensions
        $lastColumn = $col;
        $currentColumn = 'D';
        while ($currentColumn != $lastColumn) {
            $sheet->getColumnDimension($currentColumn)->setWidth(12);
            $currentColumn++;
        }
        $sheet->getColumnDimension($lastColumn)->setWidth(12);

        // Create the Excel file
        $writer = new Xlsx($spreadsheet);
        $filename = 'nilai_' . str_replace(' ', '_', strtolower($course->nama_kelas)) . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportNilaiPdf($id)
    {
        $course = Course::with(['participants.kelas', 'meetings', 'mentor', 'kategori', 'program', 'sekolah'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('exports.course-scores', compact('course'));
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('nilai_' . str_replace(' ', '_', strtolower($course->nama_kelas)) . '_' . date('Y-m-d') . '.pdf');
    }
} 