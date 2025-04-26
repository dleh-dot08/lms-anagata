<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\Kategori;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class CourseController extends Controller
{
    // Menampilkan daftar kursus
    public function index(Request $request)
    {
        $query = Course::with(['mentor', 'kategori', 'jenjang']);

        // Filter berdasarkan tab aktif/nonaktif + waktu_akhir
        if ($request->tab === 'nonaktif') {
            $query->where(function ($q) {
                $q->where('status', '!=', 'Aktif')
                ->orWhere('waktu_akhir', '<', Carbon::now());
            });
        } else {
            // Default: kursus aktif dan belum lewat waktu_akhir
            $query->where('status', 'Aktif')
                ->where('waktu_akhir', '>=', Carbon::now());
        }

        // Filter berdasarkan pencarian nama kursus
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status manual jika diisi dari dropdown (opsional)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.index', compact('courses'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.index', compact('courses'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Menampilkan halaman pembuatan kursus
    public function create()
    {
        // Ambil data mentor, kategori, dan jenjang
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.create', compact('mentors', 'kategoris', 'jenjangs'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.create', compact('mentors', 'kategoris', 'jenjangs'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Menyimpan kursus baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'mentor_id' => 'required|exists:users,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjang,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);
    
        $data = $request->all();
        $data['kode_unik'] = Course::generateKodeKelas();
    
        Course::create($data);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.index')->with('success', 'kursus berhasil dibuat');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.index')->with('success', 'kursus berhasil dibuat');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }
    

    // Menampilkan halaman untuk mengedit kursus
    public function edit(Course $course)
    {
        // Ambil data mentor, kategori, dan jenjang
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.edit', compact('course', 'mentors', 'kategoris', 'jenjangs'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.edit', compact('course', 'mentors', 'kategoris', 'jenjangs'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Memperbarui kursus yang sudah ada
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'mentor_id' => 'required|exists:users,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjang,id', // <- perbaikan di sini
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        $course->update($request->only([
            'nama_kelas',
            'mentor_id',
            'kategori_id',
            'jenjang_id',
            'level',
            'status',
            'waktu_mulai',
            'waktu_akhir',
            'harga',
            'jumlah_peserta',
        ]));

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Menampilkan halaman detail kursus
    public function show(Request $request, $id)
    {
        $course = Course::with('mentor', 'kategori', 'jenjang', 'lessons')->findOrFail($id);

        $query = Enrollment::with('user.jenjang')
            ->where('course_id', $id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(10);

        // ========================== Tambahan baru di bawah sini ==========================

        // 1. Ambil semua tanggal lesson (hanya tanggal, tanpa jam)
        $lessonDates = Lesson::where('course_id', $id)
                        ->pluck('created_at')
                        ->map(function ($date) {
                            return $date->format('Y-m-d');
                        })
                        ->toArray();

        $totalLessons = count($lessonDates);

        // 2. Untuk masing-masing peserta, hitung jumlah hadir/izin yang cocok dengan tanggal lesson
        foreach ($enrollments as $enrollment) {
            $presentCount = Attendance::where('course_id', $id)
                ->where('user_id', $enrollment->user_id)
                ->whereIn('status', ['Hadir', 'Izin'])
                ->whereDate('tanggal', function ($query) use ($lessonDates) {
                    $query->whereIn('tanggal', $lessonDates);
                })
                ->count();

            $enrollment->attendance_percentage = $totalLessons > 0 ? ($presentCount / $totalLessons) * 100 : 0;
        }

        // ========================== Akhir tambahan ==========================

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.show', compact('course', 'enrollments'));

        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.show', compact('course', 'enrollments'));

        } else {
            abort(403, 'Akses dilarang.');
        }
    }


    // Menghapus kursus (soft delete)
    public function destroy(Course $course)
    {

        $course->delete();

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.apd.index')->with('success', 'Kursus berhasil dihapus.');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.index')->with('success', 'Kursus berhasil dihapus.');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function searchPeserta(Request $request, Course $course)
    {
        $term = $request->get('q');

        $existingUserIds = $course->enrollments()->pluck('user_id');

        $users = User::where('role_id', 4)
            ->whereNotIn('id', $existingUserIds)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get();

        return response()->json($users->map(fn($u) => [
            'id' => $u->id,
            'text' => "{$u->name} ({$u->email})"
        ]));
    }


    public function addParticipant(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $existing = Enrollment::where('course_id', $id)
                            ->where('user_id', $request->user_id)
                            ->first();

        if ($existing) {
            return back()->with('warning', 'Peserta sudah terdaftar dalam kursus ini.');
        }

        Enrollment::create([
            'course_id' => $id,
            'user_id' => $request->user_id,
            'mentor_id' => auth()->id(), // opsional
            'tanggal_daftar' => now(),
            'tanggal_mulai' => now(),
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function removeParticipant($id, $participant_id)
    {
        Enrollment::where('course_id', $id)
                ->where('user_id', $participant_id)
                ->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    public function indexpeserta(Request $request)
    {
        $user = Auth::user();

        $query = $user->enrolledCourses()
        ->with(['kategori', 'jenjang', 'mentor'])
            // Filter hanya kursus yang sedang aktif
            ->where('status', 'aktif')
            ->whereDate('waktu_mulai', '<=', now())
            ->whereDate('waktu_akhir', '>=', now())
            // Pencarian nama kursus
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->search . '%');
            })
            // Filter status (jika ingin override filter status di atas)
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('waktu_mulai', 'desc');

        $courses = $query->paginate(10)->appends($request->all());

        return view('peserta.kursus.index', compact('courses'));
    }

    public function showPeserta($courseId)
    {
        // Menyaring kursus berdasarkan ID dan relasi yang dibutuhkan
        $course = Course::with(['kategori', 'jenjang', 'mentor', 'lessons'])
                        ->findOrFail($courseId);

        // Mengecek apakah kursus aktif
        if ($course->status != 'aktif' || \Carbon\Carbon::now()->notBetween($course->waktu_mulai, $course->waktu_akhir)) {
            return view('peserta.kursus.show', ['course' => $course, 'isActive' => false]);
        }

        return view('peserta.kursus.show', ['course' => $course, 'isActive' => true]);
    }

    // Menampilkan materi kursus untuk peserta
    public function showLesson($courseId, $lessonId)
    {
        // Menyaring kursus dan pelajaran berdasarkan ID
        $course = Course::with(['lessons' => function($query) use ($lessonId) {
                        $query->where('id', $lessonId);
                    }])
                    ->findOrFail($courseId);

        $lesson = $course->lessons->first();  // Mengambil materi yang sesuai

        // Menampilkan materi kursus
        return view('peserta.kursus.showLesson', compact('course', 'lesson'));
    }

    // Menampilkan kursus yang diajarkan oleh mentor
    public function indexMentor(Request $request)
    {
        $user = Auth::user();

        $courses = Course::with(['kategori', 'jenjang'])
            ->where('mentor_id', $user->id)
            ->when($request->search, function ($query) use ($request) {
                $query->where('nama_kelas', 'like', '%' . $request->search . '%');
            })
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10);

        return view('mentor.kursus.index', compact('courses'));
    }

    // Detail kursus untuk mentor
    public function showMentor($id)
    {
        $course = Course::with(['kategori', 'jenjang', 'lessons', 'enrollments.user'])->findOrFail($id);

        // Optional: check if this mentor owns the course
        if ($course->mentor_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('mentor.kursus.show', compact('course'));
    }

    // Detail materi untuk mentor
    public function showLessonMentor($courseId, $lessonId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->mentor_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $lesson = $course->lessons()->where('id', $lessonId)->firstOrFail();

        return view('mentor.kursus.showLesson', compact('course', 'lesson'));
    }

        // Tampilkan form gabung
        public function showJoinFormPeserta()
        {
            return view('peserta.kursus.join');
        }
    
        // Proses input kode
        public function joinWithCodePeserta(Request $request)
        {
            $request->validate([
                'kode_unik' => 'required|string|exists:courses,kode_unik',
            ], [
                'kode_unik.exists' => 'Kode kursus tidak ditemukan.',
            ]);
    
            $course = Course::where('kode_unik', $request->kode_unik)->first();
            $user   = auth()->user();

            // Cek apakah jenjang peserta sesuai dengan jenjang kursus
            if ($user->jenjang_id !== $course->jenjang_id) {
                return redirect()->route('courses.indexpeserta')
                                 ->withErrors(['kode_unik' => 'Kamu tidak memiliki jenjang yang sesuai untuk mengikuti kursus ini.']);
            }
    
            // Cek sudah terdaftar?
            if ($user->courses()->where('course_id', $course->id)->exists()) {
                return redirect()->route('courses.showPeserta', $course->id)
                                 ->with('info', 'Kamu sudah terdaftar di kursus ini.');
            }
    
            // Daftarkan user ke kursus
            $user->courses()->attach($course->id, [
                'mentor_id' => $course->mentor_id,
                'tanggal_daftar' => now(),
                'tanggal_mulai' => $course->waktu_mulai,
                'tanggal_selesai' => null,
            ]);
    
            return redirect()->route('courses.showPeserta', $course->id)
                             ->with('success', 'Berhasil bergabung ke kursus!');
        }

public function searchMentor(Request $request)
{
    $searchTerm = $request->get('q'); // Fix: match Select2's param name "q"

    // Find mentors (assuming role_id 2 is Mentor)
    $mentors = User::where('role_id', 2)
        ->where(function ($query) use ($searchTerm) {
            $query->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
        })
        ->select('id', 'name')
        ->get();

    // Return formatted for Select2: [{ id: 1, text: 'Name' }, ...]
    $results = $mentors->map(function ($mentor) {
        return [
            'id' => $mentor->id,
            'text' => $mentor->name
        ];
    });

    return response()->json($results);
}


}
