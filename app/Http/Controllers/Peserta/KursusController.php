<?php

namespace App\Http\Controllers\Peserta;

use App\Models\Course;
use App\Models\Lesson; // Pastikan model Lesson diimpor
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Tetap diimpor jika diperlukan di tempat lain

class KursusController extends Controller
{
    // Middleware 'auth' akan diterapkan di level rute (web.php)
    // Otorisasi 'view' untuk Course akan ditangani oleh CoursePolicy
    // melalui Route Model Binding, jadi tidak perlu cek manual di setiap method.

    /**
     * Menampilkan halaman overview kursus untuk peserta.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function overview(Course $course)
    {
        // Laravel secara otomatis akan menjalankan CoursePolicy@view jika diatur di rute.
        // Contoh rute: Route::get('/kursus/{course}/overview', [KursusController::class, 'overview'])->middleware('can:view,course');
        return view('peserta.kursus.overview', compact('course'));
    }

    /**
     * Menampilkan daftar pertemuan (meetings) untuk kursus tertentu.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function meetings(Course $course)
    {
        // Eager load relasi 'meetings' agar tidak terjadi N+1 query problem
        // dan untuk memastikan data pertemuan tersedia.
        $course->load('meetings');

        // Mengurutkan pertemuan berdasarkan 'pertemuan_ke' (asumsi ini kolom urutan)
        $meetings = $course->meetings->sortBy('pertemuan_ke');

        return view('peserta.kursus.meetings', compact('course', 'meetings'));
    }

    /**
     * Menampilkan daftar tugas (assignments) untuk kursus tertentu.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function assignments(Course $course)
    {
        // Eager load relasi 'assignments'
        $course->load('assignments');

        // Jika relasi assignments bisa kosong, pastikan variabel assignments selalu array
        $assignments = $course->assignments;

        return view('peserta.kursus.assignments', compact('course', 'assignments'));
    }

    /**
     * Menampilkan daftar proyek (projects) untuk kursus tertentu.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\View\View
     */
    public function projects(Course $course)
    {
        // Eager load relasi 'projects'
        $course->load('projects');
        $projects = $course->projects;

        return view('peserta.kursus.projects', compact('course', 'projects'));
    }

    /**
     * Menampilkan materi pelajaran (lesson) tertentu dalam sebuah kursus.
     *
     * @param  \App\Models\Course  $course
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showLesson(Course $course, Lesson $lesson)
    {
        // Memastikan bahwa lesson yang diminta benar-benar bagian dari course ini.
        // Jika tidak, kembalikan 404 Not Found.
        if ($lesson->course_id !== $course->id) {
            abort(404, 'Pelajaran tidak ditemukan dalam kursus ini.');
        }

        // Eager load relasi 'meeting' untuk lesson ini,
        // penting untuk Blade view yang membandingkan $lesson->meeting->id
        $lesson->load('meeting');

        // Eager load semua pertemuan untuk kursus ini,
        // diperlukan untuk navigasi dropdown pertemuan di Blade view.
        $course->load('meetings');
        $meetings = $course->meetings->sortBy('pertemuan'); // Asumsi 'pertemuan' adalah kolom untuk urutan

        return view('peserta.kursus.showLesson', compact('course', 'lesson', 'meetings'));
    }
}