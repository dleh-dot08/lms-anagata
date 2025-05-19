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
        $assignments = Assignment::with(['meeting.course'])->orderBy('created_at', 'desc')->get();

        $submissions = AssignmentSubmission::where('user_id', $user->id)->get()->keyBy('assignment_id');

        return view('assignments.peserta.index', compact('assignments', 'submissions'));
    }

    public function store(Request $request, Assignment $assignment)
    {
        $request->validate([
            'file_submission' => 'required|file|max:10240',
            'catatan' => 'nullable|string',
        ]);
    
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();
    
        if ($existing) {
            return redirect()->back()->with('error', 'Kamu sudah mengumpulkan tugas ini.');
        }
    
        // Simpan file ke storage/app/public/submissions
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
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    
        $request->validate([
            'file_submission' => 'nullable|file|max:10240',
            'catatan' => 'nullable|string',
        ]);
    
        if ($request->hasFile('file_submission')) {
            // Simpan file ke storage/app/public/submissions
            $path = $request->file('file_submission')->store('submissions', 'public');
            $submission->file_submission = $path;
        }
    
        $submission->catatan = $request->catatan;
        $submission->save();
    
        return redirect()->back()->with('success', 'Tugas berhasil diperbarui!');
    }
    
}

