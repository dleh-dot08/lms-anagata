<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Score;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Jenjang;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Kategori;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Sekolah;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class CourseController extends Controller
{
    // Menampilkan daftar kursus
    public function index(Request $request)
    {
        $query = Course::with(['mentor', 'mentor2', 'mentor3', 'kategori', 'jenjang']); // Eager load mentor2 and mentor3

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

        $courses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $user = Auth::user();

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
        $jenjangs = Jenjang::all();
        $kategoris = Kategori::all();
        $mentors = User::where('role_id', 2)->get();
        $kelas = Kelas::all();
        $sekolah = Sekolah::all();
        $programs = Program::all();

        $user = Auth::user();

        if ($user->role_id === 1) {
            return view('courses.create', compact('jenjangs', 'kategoris', 'mentors', 'kelas', 'sekolah', 'programs'));
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.create', compact('jenjangs', 'kategoris', 'mentors', 'kelas', 'sekolah', 'programs'));
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Menyimpan kursus baru ke database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'mentor_id' => 'required|exists:users,id',
            'mentor_id_2' => 'nullable|exists:users,id', // Corrected to mentor_id_2
            'mentor_id_3' => 'nullable|exists:users,id', // Corrected to mentor_id_3
            'deskripsi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjang,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'program_id' => 'nullable|exists:programs,id',
            'sekolah_id' => 'nullable|exists:sekolah,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after:waktu_mulai',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        // Add kode_unik to validated data
        $validated['kode_unik'] = Course::generateKodeKelas();

        $course = Course::create($validated);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.index')
                ->with('success', 'Kursus berhasil dibuat');
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.index')
                ->with('success', 'Kursus berhasil dibuat');
        } else {
            abort(403, 'Akses dilarang.');
        }
    }


    // Menampilkan halaman untuk mengedit kursus
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $jenjangs = Jenjang::all();
        $kategoris = Kategori::all();
        $mentors = User::where('role_id', 2)->get();
        $kelas = Kelas::all();
        $sekolah = Sekolah::all();
        $programs = Program::all();

        $user = Auth::user();

        if ($user->role_id === 1) {
            return view('courses.edit', compact('course', 'jenjangs', 'kategoris', 'mentors', 'kelas', 'sekolah', 'programs'));
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.edit', compact('course', 'jenjangs', 'kategoris', 'mentors', 'kelas', 'sekolah', 'programs'));
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
            'mentor_id_2' => 'nullable|exists:users,id', // Corrected to mentor_id_2
            'mentor_id_3' => 'nullable|exists:users,id', // Corrected to mentor_id_3
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjang,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'deskripsi' => 'required|string|max:255',
            'sekolah_id' => 'nullable|exists:sekolah,id',
            'program_id' => 'nullable|exists:programs,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after:waktu_mulai',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        $course->update($request->only([
            'nama_kelas',
            'mentor_id',
            'mentor_id_2', // Corrected to mentor_id_2
            'mentor_id_3', // Corrected to mentor_id_3
            'kategori_id',
            'jenjang_id',
            'kelas_id',
            'sekolah_id',
            'program_id',
            'deskripsi',
            'level',
            'status',
            'waktu_mulai',
            'waktu_akhir',
            'harga',
            'jumlah_peserta',
        ]));

        $user = Auth::user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');

        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.index')->with('success', 'Kursus berhasil diperbarui.');

        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    // Menampilkan halaman detail kursus
    public function show(Request $request, $id)
    {
        $course = Course::with(['mentor', 'mentor2', 'mentor3', 'kategori', 'jenjang', 'program', 'meetings', 'lessons', 'projects'])->findOrFail($id);

        // Get enrollments with pagination and search
        // PENTING: Eager load 'user', dan kemudian 'jenjang', 'sekolah', 'kelas' langsung dari model Enrollment
        // Ini akan memuat data historis yang tersimpan di tabel enrollments
        $enrollmentsQuery = $course->enrollments()->with(['user', 'jenjang', 'sekolah', 'kelas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $enrollmentsQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $enrollmentsQuery->paginate(10)->withQueryString();

        $user = Auth::user();

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

        $user = Auth::user();

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

    /**
     * Menampilkan daftar kursus yang diikuti peserta.
     * Filter berdasarkan status kursus (Aktif/Nonaktif) dan status pendaftaran (aktif/tidak_aktif).
     */
    public function indexpeserta(Request $request)
    {
        $user = Auth::user();

        $query = $user->enrolledCourses()
            ->with(['kategori', 'jenjang', 'mentor']);

        // Filter: Hanya kursus yang statusnya 'Aktif' di tabel courses
        $query->where('courses.status', 'Aktif'); // Kualifikasi 'courses.status'

        // Filter: Hanya kursus yang waktu mulainya sudah lewat dan waktu akhirnya belum lewat
        $query->whereDate('courses.waktu_mulai', '<=', Carbon::now());
        $query->whereDate('courses.waktu_akhir', '>=', Carbon::now());

        // Filter: Pencarian nama kursus
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('courses.nama_kelas', 'like', '%' . $request->search . '%'); // Kualifikasi 'courses.nama_kelas'
        });

        // Filter: Status pendaftaran peserta ('aktif'/'tidak_aktif')
        // Ini berlaku pada tabel pivot (enrollments)
        $query->wherePivot('status', 'aktif'); // Default: Hanya tampilkan kursus dengan enrollment 'aktif'

        // Jika ada permintaan filter status enrollment dari request, terapkan
        $query->when($request->filled('enrollment_status'), function ($q) use ($request) {
            $q->wherePivot('status', $request->enrollment_status);
        });

        $query->orderBy('courses.waktu_mulai', 'desc'); // Kualifikasi 'courses.waktu_mulai'

        $courses = $query->paginate(10)->withQueryString();

        return view('peserta.kursus.index', compact('courses'));
    }

    /**
     * Menampilkan detail satu kursus untuk peserta.
     * Membatasi akses berdasarkan status kursus dan status pendaftaran peserta.
     */
    public function showPeserta($courseId)
    {
        $user = Auth::user();

        // Cari kursus berdasarkan ID DAN pastikan user terdaftar di kursus ini
        // dengan enrollment yang aktif
        $course = Course::with(['kategori', 'jenjang', 'mentor', 'lessons'])
                        ->whereHas('enrollments', function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                              ->where('status', 'aktif'); // Pastikan enrollment user ini aktif
                        })
                        ->findOrFail($courseId);

        // Jika kursus ditemukan, cek status kursus itu sendiri dan rentang waktunya
        // (cek enrollment user sudah dilakukan di whereHas di atas)
        if ($course->status !== 'Aktif' || Carbon::now()->lt($course->waktu_mulai) || Carbon::now()->gt($course->waktu_akhir)) {
            abort(403, 'Kursus ini sudah tidak aktif atau belum/sudah selesai.');
        }

        // Jika semua cek berhasil, tampilkan kursus
        return view('peserta.kursus.show', ['course' => $course, 'isActive' => true]);
    }

    /**
     * Menampilkan detail satu lesson dalam kursus untuk peserta.
     * Membatasi akses berdasarkan status kursus dan status pendaftaran peserta.
     */
    public function showLesson($courseId, $lessonId)
    {
        $user = Auth::user();
    
        // Cari kursus berdasarkan ID DAN pastikan user terdaftar di kursus ini
        // dengan enrollment yang aktif
        $course = Course::with('meetings.lesson')
                        ->whereHas('enrollments', function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                              ->where('status', 'aktif'); // Pastikan enrollment user ini aktif
                        })
                        ->findOrFail($courseId); // Jika tidak ditemukan, akan 404
    
        // Setelah memastikan user terdaftar di kursus dengan enrollment aktif,
        // cek status kursus itu sendiri dan rentang waktunya
        if ($course->status !== 'Aktif' || Carbon::now()->lt($course->waktu_mulai) || Carbon::now()->gt($course->waktu_akhir)) {
            abort(403, 'Materi pelajaran ini tidak tersedia. Kursus tidak aktif atau telah berakhir.');
        }
    
        // Ambil lesson setelah semua pengecekan akses kursus berhasil
        $lesson = Lesson::with('meeting')
            ->where('course_id', $course->id) // Pastikan lesson milik course yang sudah divalidasi
            ->where('id', $lessonId)
            ->firstOrFail(); // Jika tidak ditemukan, akan 404
    
        return view('peserta.kursus.showLesson', compact('course', 'lesson'));
    }
    
    // Menampilkan kursus yang diajarkan oleh mentor
    public function indexMentor(Request $request)
    {
        $mentorId = auth()->id();
        $today = now();

        $courses = Course::with(['mentor', 'mentor2', 'mentor3', 'kategori'])
            ->where(function($query) use ($mentorId) {
                $query->where('mentor_id', $mentorId)
                      ->orWhere('mentor_id_2', $mentorId)
                      ->orWhere('mentor_id_3', $mentorId);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('nama_kelas', 'like', '%' . $request->search . '%');
            })
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(10);

        $courses->getCollection()->transform(function ($course) {
            $course->status_dinamis = now()->greaterThan(Carbon::parse($course->waktu_akhir)) ? 'Non-Aktif' : 'Aktif';
            return $course;
        });

        return view('mentor.kursus.index', [
            'courses' => $courses,
            'search' => $request->search,
            'status' => $request->status,
        ]);
    }

    // Detail kursus untuk mentor
    public function showMentor($id)
    {
        $course = Course::with(['kategori', 'jenjang', 'lessons', 'enrollments.user', 'mentor', 'mentor2', 'mentor3'])->findOrFail($id); // Eager load mentor2 and mentor3

        // Optional: check if this mentor owns the course or is a backup mentor
        if ($course->mentor_id !== Auth::id() && $course->mentor_id_2 !== Auth::id() && $course->mentor_id_3 !== Auth::id()) { // Corrected
            abort(403, 'Unauthorized access');
        }

        return view('mentor.kursus.show', compact('course'));
    }

    // Detail materi untuk mentor
    public function showLessonMentor($courseId, $lessonId)
    {
        $course = Course::findOrFail($courseId);

        // Optional: check if this mentor owns the course or is a backup mentor
        if ($course->mentor_id !== Auth::id() && $course->mentor_id_2 !== Auth::id() && $course->mentor_id_3 !== Auth::id()) { // Corrected
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
                'mentor_id' => $course->mentor_id, // Still using the primary mentor ID for enrollment
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

public function overview($courseId)
{
    $course = Course::with(['mentor', 'mentor2', 'mentor3', 'meetings', 'lessons'])->findOrFail($courseId); // Eager load mentor2 and mentor3
    return view('mentor.kursus.overview', compact('course'));
}

public function project($courseId)
{
    $course = Course::with(['projects.user'])->findOrFail($courseId);

    return view('mentor.kursus.projects', compact('course'));
}

public function showSilabus($id)
{
    $course = Course::findOrFail($id);

    // Optional: check if this mentor owns the course or is a backup mentor
    if ($course->mentor_id !== Auth::id() && $course->mentor_id_2 !== Auth::id() && $course->mentor_id_3 !== Auth::id()) { // Corrected
        abort(403, 'Akses ditolak.');
    }

    // Ubah URL menjadi embed-ready
    $course->silabus_pdf = $this->convertToGDrivePreview($course->silabus_pdf);

    return view('mentor.kursus.silabus', compact('course'));
}

public function previewSilabus($id)
{
    $course = Course::findOrFail($id);

    // Optional: check if this mentor owns the course or is a backup mentor
    if ($course->mentor_id !== Auth::id() && $course->mentor_id_2 !== Auth::id() && $course->mentor_id_3 !== Auth::id()) { // Corrected
        abort(403, 'Akses ditolak.');
    }

    $filePath = storage_path("app/public/silabus/{$course->silabus_pdf}");

    if (!file_exists($filePath)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file($filePath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $course->silabus_pdf . '"',
        'X-Frame-Options' => 'ALLOWALL', // Ini override default DENY/SAMEORIGIN
        // Jika perlu, bisa juga set CSP header di sini
        'Content-Security-Policy' => "frame-ancestors 'self' http://your-domain.com",
    ]);
}


public function uploadSilabus(Request $request, Course $course)
{
    $request->validate([
        'silabus_pdf' => 'required|url', // validasi URL
    ]);

    // Simpan langsung URL dari Google Drive
    $course->silabus_pdf = $request->input('silabus_pdf');
    $course->save();

    return redirect()->route('courses.show', $course->id)->with('success', 'Link Silabus berhasil disimpan.');
}

public function uploadRpp(Request $request, $id) {
    $course = Course::findOrFail($id);
    $course->rpp_drive_link = $request->rpp_drive_link;
    $course->save();
    return back()->with('success', 'Link RPP berhasil disimpan.');
}

private function convertToGDrivePreview($url)
{
    if (!$url) return null;

    if (str_contains($url, 'drive.google.com')) {
        // Pastikan link embed
        if (str_contains($url, '/view')) {
            return str_replace('/view', '/preview', $url);
        }

        // Jika link mentah
        if (str_contains($url, '/file/d/')) {
            return preg_replace('#/file/d/(.*?)/.*#', 'https://drive.google.com/file/d/$1/preview', $url);
        }
    }

    return $url;
}

public function showAllPeserta($id) {

    $course = Course::with('participants')->findOrFail($id);

    return view('mentor.kursus.peserta', compact('course'));
}

public function showAssignment($id)
{
    $course = Course::with(['meetings.assignments'])->findOrFail($id);
    $activeTab = 'assignment';
    return view('mentor.kursus.assignment', compact('course', 'activeTab'));
}

public function listMeetingsForScoring(Course $course)
{
    $course = Course::with(['kategori', 'jenjang', 'lessons', 'enrollments.user', 'meetings'])->findOrFail($course->id);

    // Optional: check if this mentor owns the course or is a backup mentor
    if ($course->mentor_id !== Auth::id() && $course->mentor_id_2 !== Auth::id() && $course->mentor_id_3 !== Auth::id()) { // Corrected
        abort(403, 'Unauthorized access');
    }

    $activeTab = 'scores';
    return view('mentor.kursus.score', compact('course', 'activeTab'));
}

}