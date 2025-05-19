<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function input(Course $course, Meeting $meeting)
    {
        $enrollments = $course->enrollments()->with('user')->get();
    
        // Ambil semua skor yang sudah pernah disimpan untuk meeting ini
        $existingScores = Score::where('meeting_id', $meeting->id)
            ->get()
            ->keyBy('peserta_id'); // supaya akses cepat berdasarkan peserta_id
    
        return view('mentor.kursus.inputScore', compact('course', 'meeting', 'enrollments', 'existingScores'));
    }
    

    public function store(Request $request, Course $course, Meeting $meeting)
    {
        $data = $request->validate([
            'scores' => 'required|array',
            'scores.*.peserta_id' => 'required|exists:users,id',
            'scores.*.creativity_score' => 'required|numeric|min:0|max:100',
            'scores.*.design_score' => 'required|numeric|min:0|max:100',
            'scores.*.program_score' => 'required|numeric|min:0|max:100',
        ]);
    
        foreach ($data['scores'] as $scoreData) {
            $average = round((
                $scoreData['creativity_score'] +
                $scoreData['design_score'] +
                $scoreData['program_score']
            ) / 3, 2);
    
            Score::updateOrCreate(
                [
                    'peserta_id' => $scoreData['peserta_id'],
                    'meeting_id' => $meeting->id,
                ],
                [
                    'creativity_score' => $scoreData['creativity_score'],
                    'design_score' => $scoreData['design_score'],
                    'program_score' => $scoreData['program_score'],
                    'total_score' => $average,
                    'mentor_id' => auth()->id(),
                ]
            );
        }
    
        return redirect()->route('mentor.scores.index', [$course->id, $meeting->id])
            ->with('success', 'Nilai berhasil disimpan.');
    }
    
}

