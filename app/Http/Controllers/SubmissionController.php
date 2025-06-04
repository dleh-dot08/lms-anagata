<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;

class SubmissionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Dapatkan ID kursus yang diikuti oleh siswa
        $enrolledCourseIds = $user->enrolledCourses()->pluck('courses.id');
        
        // Ambil tugas hanya dari kursus yang diikuti oleh siswa
        $assignments = Assignment::with(['meeting.course'])
            ->whereHas('meeting.course', function($query) use ($enrolledCourseIds) {
                $query->whereIn('courses.id', $enrolledCourseIds);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $submissions = AssignmentSubmission::where('user_id', $user->id)
            ->get()
            ->keyBy('assignment_id');

        return view('assignments.peserta.index', compact('assignments', 'submissions'));
    }

    public function store(Request $request, Assignment $assignment)
    {
        // Cek apakah sudah melewati deadline
        if ($assignment->deadline && now()->gt($assignment->deadline)) {
            return redirect()->back()->with('error', 'Deadline telah lewat. Kamu tidak bisa mengumpulkan tugas.');
        }
    
        $request->validate([
            'file_submission' => 'required|file|max:20480',
            'catatan' => 'nullable|string',
        ], [
            'file_submission.max' => 'Ukuran file tidak boleh lebih dari 20MB.',
        ]);
    
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();
    
        if ($existing) {
            return redirect()->back()->with('error', 'Kamu sudah mengumpulkan tugas ini.');
        }
    
        $path = $request->file('file_submission')->store('submissions', 'public');
    
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'file_submission' => $path,
            'catatan' => $request->catatan,
        ]);
    
        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
    
    
    public function update(Request $request, Assignment $assignment)
    {
        // Cek apakah sudah melewati deadline
        if ($assignment->deadline && now()->gt($assignment->deadline)) {
            return redirect()->back()->with('error', 'Deadline telah lewat. Kamu tidak bisa mengedit tugas.');
        }
    
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    
        $request->validate([
            'file_submission' => 'nullable|file|max:20480',
            'catatan' => 'nullable|string',
        ], [
            'file_submission.max' => 'Ukuran file tidak boleh lebih dari 20MB.',
        ]);
    
        if ($request->hasFile('file_submission')) {
            $path = $request->file('file_submission')->store('submissions', 'public');
            $submission->file_submission = $path;
        }
    
        $submission->catatan = $request->catatan;
        $submission->save();
    
        return redirect()->back()->with('success', 'Tugas berhasil diperbarui!');
    }    
    
}

