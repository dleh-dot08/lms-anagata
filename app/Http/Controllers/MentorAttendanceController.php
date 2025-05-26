<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\Enrollment;

class MentorAttendanceController extends Controller
{
    /**
     * Menampilkan daftar kursus yang diajar mentor dan belum diabsen hari ini.
     */
    public function courses()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
    
        if ($user->role_id != 2) {
            abort(403, 'Hanya mentor yang dapat mengakses halaman ini.');
        }
    
        // Ambil semua kursus yang diajar dan aktif hari ini
        $courses = $user->coursesTaught->filter(function ($course) use ($today) {
            return $course->waktu_mulai <= $today && $course->waktu_akhir >= $today;
        });
    
        return view('attendances.mentor.mentor_attandances.course', compact('courses'));
    }
    

    /**
     * Menampilkan daftar pertemuan dari kursus tertentu.
     */
    public function meetings(Course $course)
    {
        $user = Auth::user();
    
        $meetings = $course->meetings()->orderBy('pertemuan')->get();
    
        // Ambil semua meeting_id yang sudah diabsen mentor (tanpa filter tanggal)
        $attendedMeetingIds = Attendance::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->pluck('meeting_id')
            ->toArray();
    
        return view('attendances.mentor.mentor_attandances.meeting', compact('course', 'meetings', 'attendedMeetingIds'));
    }
    
    

    /**
     * Menampilkan form absen mentor.
     */
    public function create(Course $course, Meeting $meeting)
    {
        return view('attendances.mentor.mentor_attandances.absen', compact('course', 'meeting'));
    }

    /**
     * Simpan data absen mentor.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role_id != 2) {
            abort(403, 'Hanya mentor yang dapat melakukan absen.');
        }

        $rules = [
            'course_id' => 'required|exists:courses,id',
            'meeting_id' => 'required|exists:meetings,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
            'waktu_absen' => $request->status === 'Hadir' ? 'required|date' : 'nullable',
        ];
    
        if ($request->status === 'Hadir') {
            $rules['waktu_absen'] = 'required|date';
        } else {
            $rules['waktu_absen'] = 'nullable';
        }
    
        $validated = $request->validate($rules);
    
        $userId = auth()->id();
        $meetingId = $validated['meeting_id'];
        $today = \Carbon\Carbon::parse($validated['tanggal'])->toDateString();
    
        // Cek apakah sudah pernah absen untuk meeting ini
        $exists = Attendance::where('user_id', $userId)
        ->where('course_id', $validated['course_id'])
        ->where('meeting_id', $validated['meeting_id'])
        ->exists();

        if ($exists) {
            return redirect()->back()->with('warning', 'Anda sudah mengisi absen untuk pertemuan ini.');
        }
    
        // Simpan absensi tanpa enrollment_id
        Attendance::create([
            'user_id'     => $userId,
            'course_id'   => $validated['course_id'],
            'meeting_id'  => $meetingId,
            'tanggal'     => $today,
            'status'      => $validated['status'],
            'waktu_absen' => $validated['waktu_absen'] ?? now(),
        ]);
    
        return redirect()->route('mentor.absen.courses')->with('success', 'Absen berhasil disimpan.');
    }
    
}
