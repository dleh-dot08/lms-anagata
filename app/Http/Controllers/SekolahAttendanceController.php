<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Activity;
use App\Models\Attendance;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SekolahAttendanceController extends Controller
{
    /**
     * Menampilkan daftar absensi siswa dari sekolah yang sama dengan user yang login
     */
    public function index(Request $request)
    {
        // Mendapatkan ID sekolah dari user yang login
        $sekolahId = Auth::user()->sekolah_id;
    
        if (!$sekolahId) {
            return redirect()->back()->with('error', 'Anda tidak terhubung dengan sekolah manapun.');
        }
        
        // Ambil daftar kursus yang dimiliki oleh sekolah tersebut
        $courses = Course::where('sekolah_id', $sekolahId)
            ->orderBy('nama_kelas')
            ->get();
    
        // Query absensi yang berasal dari kursus milik sekolah tersebut
        $query = Attendance::with(['user', 'course', 'activity'])
            ->whereHas('course', function ($q) use ($sekolahId) {
                $q->where('sekolah_id', $sekolahId);
            });
    
        // Filter tambahan
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
    
        if ($request->tipe === 'kursus') {
            $query->whereNotNull('course_id')->whereNull('activity_id');
        } elseif ($request->tipe === 'kegiatan') {
            $query->whereNotNull('activity_id')->whereNull('course_id');
        }
        
        // Filter berdasarkan kursus
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        $attendances = $query->latest()->paginate(10);
        
        // Mempertahankan parameter filter saat pagination
        $attendances->appends($request->all());
    
        return view('attendances.sekolah.index', compact('attendances', 'courses'));
    }
    

    /**
     * Menampilkan detail absensi siswa
     */
    public function show(Attendance $attendance)
    {
        $sekolahId = Auth::user()->sekolah->id ?? null;
    
        // Cek apakah absensi adalah kursus dan apakah kursus tersebut milik sekolah user yang login
        if (
            $attendance->course_id === null || // pastikan ini absensi kursus
            $attendance->course->sekolah_id !== $sekolahId
        ) {
            return redirect()->route('attendances.sekolah.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat data absensi ini.');
        }
    
        return view('attendances.sekolah.show', compact('attendance'));
    }
    
}