<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorAttendanceReportController extends Controller
{
    public function index()
    {
        $mentorId = auth()->id();
        $today = now();

        $courses = auth()->user()->taughtCourses()
        ->whereDate('waktu_mulai', '<=', $today)
        ->whereDate('waktu_akhir', '>=', $today)
        ->get();

        return view('laporan.absensi.mentor.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $students = $course->students()->with(['attendances' => function ($query) use ($course) {
            $query->where('course_id', $course->id);
        }])->get();

        $meetings = $course->meetings()->orderBy('pertemuan')->get();

        return view('laporan.absensi.mentor.recap', compact('course', 'students', 'meetings'));
    }

    public function selfAttendance(Request $request)
    {
        $mentorId = Auth::id(); // Ambil ID mentor yang sedang login

        // Dapatkan semua kursus yang diajar oleh mentor ini untuk filter dropdown
        $mentorCourses = Course::where('mentor_id', $mentorId)->get();

        // Ambil course_id dari request untuk filter
        $selectedCourseId = $request->input('course_id');

        // Query dasar untuk absensi mentor
        $query = Attendance::where('user_id', $mentorId)
                                 ->whereHas('meeting', function ($q) {
                                     // Memastikan pertemuan ada dan pertemuan tersebut memiliki kursus yang terkait
                                     $q->has('course');
                                 })
                                 ->with(['meeting.course']); // Load relasi pertemuan dan kelasnya

        // Terapkan filter jika course_id dipilih
        if ($selectedCourseId) {
            $query->whereHas('meeting.course', function ($q) use ($selectedCourseId) {
                $q->where('id', $selectedCourseId);
            });
        }

        // Urutkan berdasarkan tanggal pertemuan (menggunakan kolom yang benar: attendance_date)
        $absensiMentor = $query->orderBy('tanggal', 'desc')->get();

        // Mengelompokkan absensi berdasarkan kelas untuk tampilan yang lebih rapi
        $groupedAbsensi = $absensiMentor->groupBy(function($item) {
            // Karena kita sudah memastikan meeting->course ada via whereHas, ini seharusnya aman
            return $item->meeting->course->nama_kelas;
        });

        return view('laporan.absensi.mentor.self', compact('groupedAbsensi', 'mentorCourses', 'selectedCourseId'));
    }
}

