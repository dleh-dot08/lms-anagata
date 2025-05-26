<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use App\Models\MentorNote;
use Illuminate\Http\Request;

class AdminMentorNoteController extends Controller
{
    /**
     * Display a listing of courses with mentor notes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = Course::with(['mentor', 'meetings', 'meetings.note'])
            ->whereHas('meetings', function($q) {
                $q->whereHas('note');
            });
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('mentor', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $courses = $query->latest()->paginate(12);
        
        return view('jurnal.admin.index', compact('courses', 'search'));
    }

    /**
     * Display a listing of meetings for a specific course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function meetings($courseId)
    {
        $course = Course::with(['mentor', 'kategori', 'jenjang', 'enrollments'])
            ->findOrFail($courseId);
            
        $meetings = Meeting::with('note')
            ->where('course_id', $courseId)
            ->orderBy('pertemuan', 'asc')
            ->get();
            
        return view('jurnal.admin.meetings', compact('course', 'meetings'));
    }

    /**
     * Display the specified mentor note.
     *
     * @param  int  $meetingId
     * @return \Illuminate\Http\Response
     */
    public function show($meetingId)
    {
        $meeting = Meeting::with(['course', 'course.mentor', 'course.kategori'])
            ->findOrFail($meetingId);
            
        $note = MentorNote::where('meeting_id', $meetingId)->firstOrFail();
        
        return view('jurnal.admin.show', compact('meeting', 'note'));
    }
}