<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'meeting_id' => 'required|exists:meetings,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_attachment' => 'nullable|file|mimes:pdf,docx,zip',
            'deadline' => 'nullable|date',
        ]);

        $data = $request->only(['meeting_id', 'judul', 'deskripsi', 'deadline']);

        if ($request->hasFile('file_attachment')) {
            $data['file_attachment'] = $request->file('file_attachment')->store('assignments');
        }

        Assignment::create($data);

        return back()->with('success', 'Tugas berhasil dibuat.');
    }

}
