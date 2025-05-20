<?php

namespace App\Exports;

use App\Models\Score;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ScoreRecapExport implements FromView
{
    protected $course;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function view(): View
    {
        $meetings = $this->course->meetings()->orderBy('pertemuan')->get();
        $enrollments = $this->course->enrollments()->with('user')->get();
        $scores = Score::whereIn('meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy(fn($s) => $s->peserta_id . '-' . $s->meeting_id);

        return view('mentor.scores.recap-excel', [
            'course' => $this->course,
            'meetings' => $meetings,
            'enrollments' => $enrollments,
            'scores' => $scores
        ]);
    }
}

