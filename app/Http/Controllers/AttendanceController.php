<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Course;
use App\Models\Jenjang;
use App\Models\Meeting;
use App\Models\Sekolah;
use App\Models\Activity;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\AttendancesExport;

class AttendanceController extends Controller
{
    // ADMIN - Lihat semua absensi dengan filter tanggal
    public function adminIndex(Request $request)
    {
        $query = Attendance::with([
            'user.kelas',
            'user.sekolah',
            'user.jenjang',
            'course',
            'activity',
            'recordedByMentor' // <-- Add this line to eager load the recorder's details
        ]);
    
        // Filter yang sudah ada
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
    
        if ($request->tipe === 'kursus') {
            $query->whereNotNull('course_id')->whereNull('activity_id');
        } elseif ($request->tipe === 'kegiatan') {
            $query->whereNotNull('activity_id')->whereNull('course_id');
        }
    
        if ($request->role === 'mentor') {
            $query->whereHas('user', fn($q) => $q->where('role_id', 2));
        } elseif ($request->role === 'peserta') {
            $query->whereHas('user', fn($q) => $q->where('role_id', 3));
        }
    
        // Filter tambahan
        if ($request->filled('kelas')) {
            $query->whereHas('user', fn($q) => $q->where('kelas_id', $request->kelas));
        }
    
        if ($request->filled('sekolah')) {
            $query->whereHas('user', fn($q) => $q->where('sekolah_id', $request->sekolah));
        }
    
        if ($request->filled('jenjang')) {
            $query->whereHas('user', fn($q) => $q->where('jenjang_id', $request->jenjang));
        }
    
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
    
        $attendances = $query->latest()->paginate(10);
    
        // Data untuk dropdown filter
        $kelas = Kelas::all();
        $sekolah = Sekolah::all();
        $jenjang = Jenjang::all();
        $courses = Course::all();
    
        return view('attendances.admin.index', compact('attendances', 'kelas', 'sekolah', 'jenjang', 'courses'));
    }

    public function exportAttendances(Request $request)
    {
        $exportType = $request->input('export_type');
        $selectedCourseId = $request->input('export_course_id');
        $selectedSchoolId = $request->input('export_school_id');
        $startDate = $request->input('export_start_date');
        $endDate = $request->input('export_end_date');

        $filename = 'Absensi';
        $dateSuffix = date('Ymd_His');

        // Tambahkan nama kursus/sekolah ke nama file jika spesifik
        if ($exportType === 'selected_course' && $selectedCourseId) {
            $course = Course::find($selectedCourseId);
            if ($course) {
                // Pastikan nama kursus tidak mengandung karakter yang tidak valid untuk nama file
                $safeCourseName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $course->nama_kelas);
                $filename .= '_Kursus_' . $safeCourseName;
            }
        } elseif ($exportType === 'selected_school' && $selectedSchoolId) {
            $school = Sekolah::find($selectedSchoolId);
            if ($school) {
                // Pastikan nama sekolah tidak mengandung karakter yang tidak valid untuk nama file
                $safeSchoolName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $school->nama_sekolah);
                $filename .= '_Sekolah_' . $safeSchoolName;
            }
        }

        $filename .= '_' . $dateSuffix . '.xlsx';

        return Excel::download(
            new AttendancesExport(
                $exportType,
                $selectedCourseId,
                $selectedSchoolId,
                $startDate,
                $endDate
            ),
            $filename
        );
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
            'photo_capture' => 'nullable|string', // tambahkan ini
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
    
        if ($request->filled('photo_capture')) {
            $file = $this->saveBase64Image($request->photo_capture, 'absensi/foto');
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
    
        $fileName = Str::uuid() . '.' . $type;
        $publicPath = public_path('storage/' . $path);
    
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }
    
        $fullPath = $publicPath . '/' . $fileName;
    
        // === Ini fix ===
        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    
        $image = $manager->read($data);
    
        if ($image->width() > 1200) {
            $image->resize(width: 1200);
        }
    
        $image->save($fullPath);
    
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

        switch ($user->role_id) {
            case 2: // Mentor
                return view('attendances.mentor.index', compact('courses', 'attendances'));
            case 3: // Peserta
                return view('attendances.peserta.index', compact('courses', 'attendances'));
            default:
            return view('attendances.peserta.activities', compact('activities', 'activityAttendances'));
        }
    }


    public function input(Course $course)
    {
        $mentorId = auth()->id();
        
        // Cek apakah mentor punya akses ke course ini
        if ($course->mentor_id != $mentorId) {
            abort(403);
        }

        $students = $course->participants; // Asumsikan relasi many-to-many: course -> participants
        return view('attendances.mentor.input', compact('course', 'students'));
    }

    public function storeInput(Request $request, Course $course)
    {
        $request->validate([
            'absences' => 'required|array',
            'absences.*.user_id' => 'required|exists:users,id',
            'absences.*.status' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
        ]);

        foreach ($request->absences as $absence) {
            Attendance::create([
                'user_id' => $absence['user_id'],
                'course_id' => $course->id,
                'tanggal' => now(),
                'status' => $absence['status'],
            ]);
        }

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil disimpan!');
    }

       /**
     * Menampilkan daftar kursus yang diajar mentor untuk dipilih absen
     */
    public function indexMentor()
    {
        $today = Carbon::today();
    
        $courses = auth()->user()->mentorCourses()
            ->whereDate('waktu_mulai', '<=', $today)
            ->whereDate('waktu_akhir', '>=', $today)
            ->withCount(['meetings', 'enrollments'])
            ->orderBy('nama_kelas')
            ->get();
    
        return view('attendances.mentor.courses', compact('courses'));
    }

    /**
     * Menampilkan daftar pertemuan dari kursus yang dipilih
     */
    public function selectMeeting(Course $course)
    {
        $meetings = $course->meetings()
            ->orderBy('pertemuan')
            ->withCount(['attendances as absensi_diisi']) // alias tetap, tanpa filter mentor
            ->get();
    
        return view('attendances.mentor.select_meeting', compact('course', 'meetings'));
    }
    
    

    /**
     * Menampilkan form absensi untuk pertemuan tertentu
     */
    public function createMentor(Course $course, Meeting $meeting)
    {
        // Cek apakah pertemuan milik kursus yang benar
        if ($meeting->course_id !== $course->id) {
            abort(404);
        }

        $enrollments = $course->enrollments()
            ->with(['user', 'attendances' => function($q) use ($meeting) {
                $q->where('meeting_id', $meeting->id);
            }])
            ->get();
            
        return view('attendances.mentor.create_attandance', compact('course', 'meeting', 'enrollments'));
    }

    /**
     * Menyimpan data absensi
     */
    public function storeMentor(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'meeting_id' => 'required|exists:meetings,id',
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:Hadir,Izin,Sakit,Tidak Hadir',
            'waktu_absen' => 'required|array',
            'waktu_absen.*' => 'required|date',
        ]);
        
        DB::transaction(function () use ($validated) {
            foreach ($validated['status'] as $enrollmentId => $status) {
                $enrollment = \App\Models\Enrollment::findOrFail($enrollmentId);
                Attendance::updateOrCreate(
                    [
                        'meeting_id' => $validated['meeting_id'],
                        'enrollment_id' => $enrollmentId,
                    ],
                    [
                        'course_id' => $validated['course_id'],
                        'tanggal' => $validated['tanggal'],
                        'status' => $status,
                        'recorded_by_user_id' => auth()->id(), // <--- UBAH DI SINI!
                        'waktu_absen' => $validated['waktu_absen'][$enrollmentId],
                        'user_id' => $enrollment->user_id, // ID siswa/peserta
                    ]
                );
            }
        });
        
        return redirect()
            ->route('mentor.attendances.select_meeting', $validated['course_id'])
            ->with('success', 'Absensi berhasil disimpan');
    }    
    

    /**
     * Menampilkan riwayat absensi per kursus
     */
    public function history(Course $course)
    {
        $attendances = Attendance::where('course_id', $course->id)
            ->where('mentor_id', auth()->id())
            ->with(['meeting', 'user'])
            ->latest()
            ->paginate(10);

        return view('mentor.attendances.history', compact('course', 'attendances'));
    }

}
