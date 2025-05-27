<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{

    public function store(Request $request, $courseId)
    {
        $validated = $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_attachment' => 'nullable|file|mimes:pdf,docx,zip|max:10240',
            'deadline' => 'nullable|date',
        ]);
    
        if ($request->hasFile('file_attachment')) {
            // Simpan file ke storage/app/public/assignments
            $validated['file_attachment'] = $request->file('file_attachment')->store('assignments', 'public');
        }
    
        Assignment::create($validated);
    
        return back()->with('success', 'Tugas berhasil dibuat.');
    }
    
    
    public function submissions($assignmentId)
    {
        $assignment = Assignment::with('submissions.user')->findOrFail($assignmentId);
        return view('mentor.kursus.submission', compact('assignment'));
    }
}
