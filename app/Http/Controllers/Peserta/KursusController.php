<?php

namespace App\Http\Controllers\Peserta;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Meeting; // Asumsi Meeting ada dan digunakan
use App\Models\Project; // Asumsi Project ada dan digunakan
use App\Models\Assignment; // Asumsi Assignment ada dan digunakan
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KursusController extends Controller
{
    private function validateCourseAccess($courseId)
    {
        $user = Auth::user();


        $course = Course::with(['kategori', 'jenjang', 'mentor', 'lessons'])
                        ->whereHas('enrollments', function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                              ->where('status', 'aktif');
                        })
                        ->where('id', $courseId) 
                        ->first(); 
        if (!$course) {
            abort(404, 'Kursus tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($course->status !== 'Aktif' || Carbon::now()->lt($course->waktu_mulai) || Carbon::now()->gt($course->waktu_akhir)) {
            abort(403, 'Kursus ini sudah tidak aktif atau belum/sudah selesai.');
        }

        return $course;
    }

    /**
     * Menampilkan halaman overview kursus untuk peserta.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function overview($courseId)
    {
        $course = $this->validateCourseAccess($courseId); // Panggil fungsi validasi

        return view('peserta.kursus.overview', compact('course'));
    }

    /**
     * Menampilkan daftar pertemuan (meetings) untuk kursus tertentu.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function meetings($courseId)
    {
        $course = $this->validateCourseAccess($courseId);
        $course->load('meetings');

        $meetings = $course->meetings->sortBy('pertemuan');

        return view('peserta.kursus.meetings', compact('course', 'meetings'));
    }

    /**
     * Menampilkan daftar tugas (assignments) untuk kursus tertentu.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function assignments($courseId)
    {
        $course = $this->validateCourseAccess($courseId); // Panggil fungsi validasi

        $course->load('assignments');

        $assignments = $course->assignments;

        return view('peserta.kursus.assignments', compact('course', 'assignments'));
    }

    /**
     * Menampilkan daftar proyek (projects) untuk kursus tertentu.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function projects($courseId)
    {
        $course = $this->validateCourseAccess($courseId);
        $course->load('projects');
        $projects = $course->projects;

        return view('peserta.kursus.projects', compact('course', 'projects'));
    }

    /**
     * Menampilkan materi pelajaran (lesson) tertentu dalam sebuah kursus.
     *
     * @param  int  $courseId
     * @param  \App\Models\Lesson  $lesson
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function showLesson($courseId, Lesson $lesson)
    {
        $course = $this->validateCourseAccess($courseId); 
        if ($lesson->course_id !== $course->id) {
            abort(404, 'Pelajaran tidak ditemukan dalam kursus ini.');
        }

        $lesson->load('meeting');

        $course->load('meetings');
        $meetings = $course->meetings->sortBy('pertemuan');

        return view('peserta.kursus.showLesson', compact('course', 'lesson', 'meetings'));
    }
}