<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course; // Pastikan ini diimpor

class MentorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Pastikan pengguna login dan memiliki role_id 2 (Mentor)
        if (!Auth::check() || $user->role_id !== 2) {
            return redirect('/')->with('error', 'Akses ditolak. Anda bukan mentor.');
        }

        // Jika ada parameter 'course' di route
        if ($routeCourseParameter = $request->route('course')) {
            $courseId = null;

            // Periksa apakah parameter 'course' adalah instance dari model Course (jika route model binding digunakan)
            if ($routeCourseParameter instanceof Course) {
                $course = $routeCourseParameter;
            }
            // Atau jika itu adalah array dan berisi kunci 'id'
            else if (is_array($routeCourseParameter) && isset($routeCourseParameter['id'])) {
                $courseId = $routeCourseParameter['id'];
                $course = Course::find($courseId);
            }
            // Atau jika itu adalah ID langsung
            else {
                $courseId = $routeCourseParameter;
                $course = Course::find($courseId);
            }


            // Izinkan akses jika kursus ditemukan dan user adalah mentor utama atau mentor cadangan
            if ($course && ($course->mentor_id == $user->id ||
                $course->mentor_id_2 == $user->id ||
                $course->mentor_id_3 == $user->id)) {
                return $next($request);
            }

            // Jika tidak ada akses ke kursus spesifik
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke kursus ini.');
        }

        // Jika tidak ada parameter course (misalnya, halaman daftar kursus mentor), izinkan akses
        return $next($request);
    }
}