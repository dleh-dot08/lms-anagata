<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data mentor dari users dengan role_id = 2
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();
        // Query untuk mengambil courses
        $courses = Course::query();
        // Pencarian berdasarkan nama kelas
        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $courses->where('nama_kelas', 'like', "%{$search}%");
        }

        // Filter berdasarkan status (Aktif / Nonaktif)
        if ($request->has('status') && $request->get('status') != '') {
            $status = $request->get('status');
            $courses->where('status', $status);
        }

        // Menampilkan kursus aktif atau nonaktif berdasarkan tab yang dipilih
        if ($request->has('tab') && $request->get('tab') == 'nonaktif') {
            $courses->onlyTrashed(); // Untuk kursus yang sudah dihapus (soft delete)
        }

        // Paginate results
        $courses = $courses->paginate(10);

        return view('courses.index', compact('courses', 'mentors', 'kategoris', 'jenjangs'));
    }



    public function create()
    {
        $mentors = User::where('role_id', 2)->get();
        $kategoris = Kategori::all();
        $jenjangs = Jenjang::all();

        return view('courses.create', compact('mentors', 'kategoris', 'jenjangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'nama_kelas' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after:waktu_mulai',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Course berhasil dibuat.');
    }

    public function edit(Course $course)
    {
        $mentors = User::where('role_id', 3)->get();
        $categories = Kategori::all();
        $jenjangs = Jenjang::all();

        return view('courses.edit', compact('course', 'mentors', 'categories', 'jenjangs'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'nama_kelas' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'status' => 'required|in:Aktif,Nonaktif',
            'waktu_mulai' => 'required|date',
            'waktu_akhir' => 'required|date|after:waktu_mulai',
            'harga' => 'nullable|numeric',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Course berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course berhasil dihapus.');
    }

    public function restore($id)
    {
        $course = Course::withTrashed()->findOrFail($id);
        $course->restore();

        return redirect()->route('courses.index')->with('success', 'Course berhasil dipulihkan.');
    }
}
