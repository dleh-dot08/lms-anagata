<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
        $courses = Course::all();
        return view('courses.show', compact('courses')); // Ubah 'nama_view_detail' menjadi 'courses.show'
    }

    // Menghapus kursus (soft delete)
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Kursus berhasil dihapus.');
    }
}
