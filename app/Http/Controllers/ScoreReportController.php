<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Score;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScoreRecapExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ScoreReportController extends Controller
{
    public function index(Course $course)
    {
        $meetings = $course->meetings()->orderBy('pertemuan')->get();
        $enrollments = $course->enrollments()->with('user')->get();

        // Load scores keyed by user_id & meeting_id
        $scores = Score::whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy(fn($s) => $s->peserta_id . '-' . $s->meeting_id);


        return view('mentor.kursus.recap', compact('course', 'meetings', 'enrollments', 'scores'));
    }

    public function exportExcel(Course $course)
    {
        return Excel::download(new ScoreRecapExport($course), 'rekap-nilai-'.$course->nama_kelas.'.xlsx');
    }

    public function exportPdf(Course $course)
    {
        $meetings = $course->meetings()->orderBy('pertemuan')->get();
        $enrollments = $course->enrollments()->with('user')->get();
        $scores = Score::whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy(fn($s) => $s->peserta_id . '-' . $s->meeting_id);
        $pdf = PDF::loadView('mentor.scores.recap-pdf', compact('course', 'meetings', 'enrollments', 'scores'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('rekap-nilai-'.$course->nama_kelas.'.pdf');
    }
}
