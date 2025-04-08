<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // Menampilkan daftar kursus
    public function index(Request $request)
    {
        // Ambil data mentor
        $mentors = User::where('role_id', 2)->get();
        // Ambil data kategori dan jenjang
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        // Menyaring kursus berdasarkan pencarian
        $courses = Course::query();
        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $courses->where('nama_kelas', 'like', "%{$search}%");
        }
        $courses = $courses->paginate(10);

        return view('courses.index', compact('courses', 'mentors', 'kategoris', 'jenjangs'));
    }

    // Menampilkan halaman pembuatan kursus
    public function create()
    {
        // Ambil data mentor, kategori, dan jenjang
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        return view('courses.create', compact('mentors', 'kategoris', 'jenjangs'));
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

        Course::create($request->all());

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dibuat.');
    }

    // Menampilkan halaman untuk mengedit kursus
    public function edit(Course $course)
    {
        // Ambil data mentor, kategori, dan jenjang
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        return view('courses.edit', compact('course', 'mentors', 'kategoris', 'jenjangs'));
    }

    // Memperbarui kursus yang sudah ada
    public function update(Request $request, Course $course)
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

        $course->update($request->all());

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
    }

    // Menampilkan halaman detail kursus
    public function show($id)
    {
        $course = Course::findOrFail($id);

        // Ambil daftar enrollments untuk course ini
        $enrollments = Enrollment::with('user.jenjang')
            ->where('course_id', $id)
            ->get();

        // Ambil semua user_id yang sudah terdaftar di enrollments
        $enrolledUserIds = $enrollments->pluck('user_id')->toArray();

        // Ambil peserta (role_id = 4) yang belum terdaftar
        $participants = User::where('role_id', 4)
            ->whereNotIn('id', $enrolledUserIds)
            ->get();

        return view('courses.show', compact('course', 'participants', 'enrollments'));
    }

    // Menghapus kursus (soft delete)
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus.');
    }

    public function addParticipant(Request $request, $courseId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $existing = Enrollment::where('course_id', $courseId)
                    ->where('user_id', $request->user_id)
                    ->first();

        if ($existing) {
            return redirect()->back()->with('warning', 'Peserta sudah terdaftar di kursus ini.');
        }

        Enrollment::create([
            'user_id' => $request->user_id,
            'mentor_id' => $request->mentor_id, // bisa juga ambil dari course
            'course_id' => $courseId,
            'tanggal_mulai' => now(),
            'tanggal_daftar' => now(),
        ]);

        return redirect()->route('courses.show', $courseId)->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function searchPeserta(Request $request)
    {
        $query = $request->q;

        $users = User::where('role_id', 3)
                    ->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('email', 'LIKE', '%' . $query . '%');
                    })
                    ->select('id', 'name', 'email')
                    ->limit(20)
                    ->get();

        $results = $users->map(function($user) {
            return [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->email . ')'
            ];
        });

        return response()->json($results);
    }


    public function removeParticipant($courseId, $enrollmentId)
    {
        $enrollment = Enrollment::where('course_id', $courseId)
                                ->where('id', $enrollmentId)
                                ->firstOrFail();

        $enrollment->delete();

        return redirect()->route('courses.show', $courseId)->with('success', 'Peserta berhasil dihapus.');
    }



}
