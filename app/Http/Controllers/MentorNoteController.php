<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use App\Models\MentorNote;
use Illuminate\Http\Request;

class MentorNoteController extends Controller
{
    public function index()
    {
        $today = now();
    
        $courses = auth()->user()->taughtCourses()
            ->whereDate('waktu_mulai', '<=', $today)
            ->whereDate('waktu_akhir', '>=', $today)
            ->get();
    
        return view('jurnal.mentor.index', compact('courses'));
    }
    

    public function meetings($courseId)
    {
        $course = Course::with('meetings')->findOrFail($courseId);
        
        // Cek apakah user adalah mentor utama atau cadangan untuk kursus ini
        $user = auth()->user();
        $isMentor = $course->mentor_id == $user->id || 
                   $course->mentor_id_2 == $user->id || 
                   $course->mentor_id_3 == $user->id;

        if (!$isMentor) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }

        return view('jurnal.mentor.meetings', compact('course'));
    }

    public function create($meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        return view('jurnal.mentor.create', compact('meeting'));
    }

    public function store(Request $request, $meetingId)
    {
        $validated = $request->validate([
            'materi' => 'nullable|string',
            'project' => 'nullable|string',
            'sikap_siswa' => 'nullable|string',
            'hambatan' => 'nullable|string',
            'solusi' => 'nullable|string',
            'masukan' => 'nullable|string',
            'lain_lain' => 'nullable|string',
        ]);

        $validated['meeting_id'] = $meetingId;

        MentorNote::create($validated);

        return redirect()->route('mentor.notes.meetings', Meeting::find($meetingId)->course_id)
            ->with('success', 'Catatan berhasil disimpan.');
    }

    public function show(Meeting $meeting)
    {
        $note = $meeting->note;
        return view('jurnal.mentor.show', compact('meeting', 'note'));
    }

    public function edit(Meeting $meeting)
    {
        $note = $meeting->note;
        return view('jurnal.mentor.edit', compact('meeting', 'note'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $request->validate([
            'materi' => 'required|string',
            'project' => 'nullable|string',
            'sikap_siswa' => 'nullable|string',
            'hambatan' => 'nullable|string',
            'solusi' => 'nullable|string',
            'masukan' => 'nullable|string',
            'lain_lain' => 'nullable|string',
        ]);

        $note = $meeting->note;

        if (!$note) {
            return redirect()->back()->with('error', 'Catatan tidak ditemukan untuk pertemuan ini.');
        }
    
        $note->update($request->all());
    
        return redirect()->route('mentor.notes.meetings', $meeting->course_id)->with('success', 'Catatan berhasil diperbarui.');
    }

}

