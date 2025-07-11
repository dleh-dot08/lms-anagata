<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $today = Carbon::today();
    
        if ($user->role_id != 2) {
            abort(403, 'Hanya mentor yang dapat mengakses halaman ini.');
        }

        // Cek apakah user adalah mentor utama atau cadangan untuk kursus ini
        $isMentor = Course::where(function($query) use ($user) {
            $query->where('mentor_id', $user->id)
                  ->orWhere('mentor_id_2', $user->id)
                  ->orWhere('mentor_id_3', $user->id);
        })->exists();

        if (!$isMentor) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }
    
        // Ambil hanya kursus yang status-nya 'Aktif'
        $courses = auth()->user()->mentorCourses()
            ->whereDate('waktu_mulai', '<=', $today)
            ->whereDate('waktu_akhir', '>=', $today)
            ->withCount(['meetings', 'enrollments'])
            ->orderBy('nama_kelas')
            ->get();
    
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
            $rules['latitude'] = 'required';
            $rules['longitude'] = 'required';
            $rules['photo_capture'] = 'required';
        } else {
            $rules['waktu_absen'] = 'nullable';
            $rules['latitude'] = 'nullable';
            $rules['longitude'] = 'nullable';
            $rules['photo_capture'] = 'nullable';
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
            return redirect()->back()->with('error', 'Anda sudah melakukan absen untuk pertemuan ini.');
        }

        // Proses foto kehadiran jika status Hadir
        $photoPath = null;
        if ($request->status === 'Hadir' && $request->filled('photo_capture')) {
            $image = $request->photo_capture;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $userId . '_' . time() . '.jpg';
            
            Storage::disk('public')->put('attendance_photos/' . $imageName, base64_decode($image));
            $photoPath = 'attendance_photos/' . $imageName;
        }
    
        // Simpan data absen
        $attendance = new Attendance([
            'user_id' => $userId,
            'course_id' => $validated['course_id'],
            'meeting_id' => $validated['meeting_id'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'waktu_absen' => $validated['waktu_absen'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'file_attache' => $photoPath,
        ]);
    
        $attendance->save();
    
        return redirect()->route('mentor.absen.meetings', $validated['course_id'])
            ->with('success', 'Absen berhasil disimpan.');
    }
}
