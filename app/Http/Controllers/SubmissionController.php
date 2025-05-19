<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function store(Request $request, Assignment $assignment)
    {
        $request->validate([
            'file_submission' => 'required|file|max:10240',
            'catatan' => 'nullable|string',
        ]);

        // Cek apakah sudah mengumpulkan sebelumnya
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Kamu sudah mengumpulkan tugas ini.');
        }

        $path = $request->file('file_submission')->store('submissions');

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'file_submission' => $path,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }

    public function submissionForm(Assignment $assignment)
    {
        $submission = $assignment->submissionBy(auth()->id());

        return view('siswa.assignments.submit', compact('assignment', 'submission'));
    }

    public function index()
    {
        $user = auth()->user();
    
        // Ambil semua kursus yang diikuti user
        $courses = $user->enrolledCourses()->pluck('courses.id');
    
        // Ambil semua meeting ID yang termasuk di course $courses
        $meetingIds = \App\Models\Meeting::whereIn('course_id', $courses)->pluck('id')->toArray();

        // Ambil assignments yang meeting_id-nya termasuk di $meetingIds
        $assignments = \App\Models\Assignment::whereIn('meeting_id', $meetingIds)
                            ->with(['meeting.course']) // relasi yang sudah kamu definisikan
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('assignments.peserta.index', compact('assignments'));
    }

}
