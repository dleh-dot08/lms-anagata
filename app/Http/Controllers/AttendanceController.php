<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Activity;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    // ADMIN - Lihat semua absensi dengan filter tanggal
    public function adminIndex(Request $request)
    {
        // Batasi hanya admin (role_id = 1) yang bisa akses
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        $query = Attendance::with(['user', 'course'])->latest('tanggal');

        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // Default: hanya tampilkan absensi hari ini
            $query->whereDate('tanggal', now()->toDateString());
        }

        $attendances = $query->paginate(20);

        return view('attendances.admin.index', compact('attendances'));
    }


    // ADMIN - Show detail absensi
    public function show(Attendance $attendance)
    {
        return view('attendances.admin.show', compact('attendance'));
    }

    // PESERTA / MENTOR - Form absensi
    public function create(Course $course)
    {
        // $today = Carbon::today()->toDateString();
        // $already = Attendance::where('user_id', Auth::id())
        //     ->where('course_id', $course->id)
        //     ->whereDate('tanggal', $today)
        //     ->exists();

        // if ($already) {
        //     return redirect()->back()->with('info', 'Kamu sudah absen hari ini.');
        // }

        return view('attendances.' . (Auth::user()->role_id == 2 ? 'mentor' : 'peserta') . '.create', compact('course'));
    }

    // PESERTA / MENTOR - Store absensi
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'nullable|required_without:activity_id|exists:courses,id',
            'activity_id' => 'nullable|required_without:course_id|exists:activities,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'status' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
            'file_attache' => 'nullable|file|max:2048',
            'ttd_digital' => 'nullable|string',
        ]);

        if (!$request->course_id && !$request->activity_id) {
            return redirect()->back()->with('error', 'Absensi harus terkait dengan Course atau Activity.');
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $user_id = Auth::id();

        $query = Attendance::where('user_id', $user_id)
            ->whereDate('tanggal', $today);

        if ($request->course_id) {
            $query->where('course_id', $request->course_id)
                ->whereNull('activity_id');
        } elseif ($request->activity_id) {
            $query->where('activity_id', $request->activity_id)
                ->whereNull('course_id');
        }

        if ($query->exists()) {
            return redirect()->back()->with('error', 'Sudah absen hari ini untuk entitas ini.');
        }

        $file = null;
        if ($request->hasFile('file_attache')) {
            $filename = uniqid() . '.' . $request->file('file_attache')->getClientOriginalExtension();
            $filePath = public_path('storage/absensi/foto');
            if (!file_exists($filePath)) mkdir($filePath, 0755, true);
            $request->file('file_attache')->move($filePath, $filename);
            $file = 'absensi/foto/' . $filename;
        }

        $ttd = null;
        if ($request->filled('ttd_digital')) {
            $ttd = $this->saveBase64Image($request->ttd_digital, 'absensi/ttd');
        }

        Attendance::create([
            'user_id' => $user_id,
            'course_id' => $request->course_id,
            'activity_id' => $request->activity_id,
            'tanggal' => $today,
            'waktu_absen' => now(),
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'file_attache' => $file,
            'ttd_digital' => $ttd,
            'status' => $request->status,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil disimpan.');
    }
    

    private function saveBase64Image($base64Image, $path)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \Exception('Invalid image type');
            }

            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception('Base64 decode failed');
            }
        } else {
            throw new \Exception('Did not match data URI with image data');
        }

        $fileName = \Str::uuid() . '.' . $type;
        $publicPath = public_path('storage/' . $path);

        // Create directory if not exists
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $fullPath = $publicPath . '/' . $fileName;

        file_put_contents($fullPath, $data);

        return $path . '/' . $fileName;
    }



    // PESERTA / MENTOR - Rekap pribadi
    public function rekap()
    {
        $attendances = Attendance::with('course')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->paginate(20);

        $folder = Auth::user()->role_id == 2 ? 'mentor' : 'peserta';
        return view("attendances.$folder.rekap", compact('attendances'));
    }

    public function courses()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        if($user->role_id == 3) { // Peserta
            // Ambil kursus yang diikuti oleh user
            $enrolledCourses = $user->enrolledCourses;
            
            // Ambil daftar course_id yang sudah diabsen oleh user hari ini
            $alreadyAbsentCourses = Attendance::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->pluck('course_id')
                ->toArray();
        
            // Filter kursus yang:
            // - belum diabsen hari ini
            // - sedang berlangsung hari ini (berdasarkan waktu_mulai dan waktu_akhir)
            $courses = $enrolledCourses->filter(function ($course) use ($alreadyAbsentCourses, $today) {
                $start = Carbon::parse($course->waktu_mulai)->toDateString();
                $end = Carbon::parse($course->waktu_akhir)->toDateString();
        
                return !in_array($course->id, $alreadyAbsentCourses)
                    && $start <= $today
                    && $end >= $today;
            });
        } elseif ($user->role_id == 2) { // Mentor
            $taughtCourses = $user->coursesTaught;

            $alreadyAbsentCourses = Attendance::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->pluck('course_id')
                ->toArray();

            $courses = $taughtCourses->filter(function ($course) use ($alreadyAbsentCourses, $today) {
                return !in_array($course->id, $alreadyAbsentCourses)
                    && $course->waktu_mulai <= $today
                    && $course->waktu_akhir >= $today;
            });
        } else {
            $courses = collect(); // Fallback for other roles
        }
        
    
        // Ambil riwayat absensi user yang terkait dengan course
        $attendances = Attendance::where('user_id', $user->id)
            ->whereNotNull('course_id')
            ->with('course')
            ->orderByDesc('tanggal')
            ->get();
    
        switch ($user->role_id) {
            case 2: // Mentor
                return view('attendances.mentor.courses', compact('courses', 'attendances'));
            case 3: // Peserta
                return view('attendances.peserta.courses', compact('courses', 'attendances'));
            default:
                return view('attendances.peserta.courses', compact('courses', 'attendances'));
        }
    }
    
    

    public function activities()
    {
        $user = auth()->user();
        $now = Carbon::now(); 
        
        // Ambil kegiatan aktif yang diikuti user dan masih dalam rentang waktu
        $activities = $user->activities()
            ->wherePivot('status', 'Aktif')
            ->where(function ($query) use ($now) {
                $query->whereDate('waktu_mulai', '<=', $now)
                      ->whereDate('waktu_akhir', '>=', $now)
                      ->whereTime('waktu_mulai', '<=', $now->format('H:i'))
                      ->whereTime('waktu_akhir', '>=', $now->format('H:i'));
            })
            ->whereDoesntHave('attendances', function ($query) use ($user, $now) {
                // Pastikan user belum absen pada kegiatan yang dimaksud di hari yang sama
                $query->where('user_id', $user->id)
                      ->whereDate('tanggal', $now->toDateString());
            })
            ->get();
    
        // Ambil riwayat absensi kegiatan user
        $activityAttendances = Attendance::where('user_id', $user->id)
            ->whereNotNull('activity_id')
            ->with('activity')
            ->latest('tanggal')
            ->get();
    
        
        switch ($user->role_id) {
            case 2: // Mentor
                return view('attendances.mentor.activities', compact('activities', 'activityAttendances'));
            case 3: // Peserta
                return view('attendances.peserta.activities', compact('activities', 'activityAttendances'));
            default:
            return view('attendances.peserta.activities', compact('activities', 'activityAttendances'));
        }
    }
    
    

    public function createActivity(Activity $activity)
    {
        $user = auth()->user();
        switch ($user->role_id) {
            case 2: // Mentor
                return view('attendances.mentor.create-activity', compact('activity'));
            case 3: // Peserta
                return view('attendances.peserta.create-activity', compact('activity'));
            default:
            return view('attendances.peserta.activities', compact('activities', 'activityAttendances'));
        }
    }

    public function storeActivity(Request $request, Activity $activity)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'status' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
            'file_attache' => 'nullable|file|max:2048',
            'ttd_digital' => 'required',
        ]);

        $user = auth()->user();

        $filePath = null;
        if ($request->hasFile('file_attache')) {
            $filePath = $request->file('file_attache')->store('absensi/lampiran', 'public');
        }

        Attendance::create([
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'tanggal' => $request->tanggal,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'file_attache' => $filePath,
            'ttd_digital' => $request->ttd_digital,
            'status' => $request->status,
            'waktu_absen' => now(),
        ]);

        return redirect()->route('attendances.activities')->with('success', 'Absensi kegiatan berhasil dikirim.');
    }


    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
    
        $enrolled = $user->enrolledCourses;
        $alreadyAbsent = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->pluck('course_id')
            ->toArray();
    
        $courses = $enrolled->filter(fn($course) => !in_array($course->id, $alreadyAbsent));

        $attendances = Attendance::where('user_id', $user->id)->with('course')->orderByDesc('tanggal')->get();
    
        return view('attendances.peserta.index', compact('courses', 'attendances'));
    }

}
