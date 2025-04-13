<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Jenjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CourseController extends Controller
{
    // Menampilkan daftar kursus
    public function index(Request $request)
    {
        $query = Course::with(['mentor', 'kategori', 'jenjang']);

        // Filter berdasarkan tab aktif/nonaktif
        if ($request->tab === 'nonaktif') {
            $query->where('status', '!=', 'Aktif');
        } else {
            $query->where('status', 'Aktif');
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

        return view('courses.index', compact('courses'));
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
            'jenjang_id' => 'required|exists:jenjangs,id', // <- perbaikan di sini
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

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil diperbarui.');
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

        return view('courses.show', compact('course', 'enrollments'));
    }

    // Menghapus kursus (soft delete)
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus.');
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


}
