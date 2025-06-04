<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course; // Pastikan ini diimpor
use App\Models\Enrollment; // Jika middleware ini juga untuk peserta

class MentorOrPesertaMiddleware
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
    
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Anda harus login untuk mengakses halaman ini.');
        }
    
        // Coba ambil parameter 'course' dari route (jika ada)
        $course = $request->route('course');
    
        if ($course instanceof \App\Models\Course) {
            // Jika ada parameter course, lakukan pengecekan mentor/peserta sesuai course
            if ($user->role_id === 2) { // Mentor
                if (
                    $course->mentor_id == $user->id ||
                    $course->mentor_id_2 == $user->id ||
                    $course->mentor_id_3 == $user->id
                ) {
                    return $next($request);
                }
            } elseif ($user->role_id === 3) { // Peserta
                $isEnrolled = \App\Models\Enrollment::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->exists();
    
                if ($isEnrolled) {
                    return $next($request);
                }
            }
    
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke kursus ini.');
        }
    
        // Jika tidak ada parameter course, cukup pastikan role_id peserta atau mentor
        if (in_array($user->role_id, [2, 3])) {
            return $next($request);
        }
    
        return redirect('/')->with('error', 'Akses ditolak.');
    }
}