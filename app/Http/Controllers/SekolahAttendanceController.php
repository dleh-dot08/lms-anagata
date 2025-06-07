<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Course;
use App\Models\Activity;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchoolCourseAttendanceExport; // Ini akan jadi nama export class baru kita!

class SekolahAttendanceController extends Controller
{
    /**
     * Menampilkan daftar absensi siswa dari sekolah yang sama dengan user yang login
     */
    public function index(Request $request)
    {
        $sekolahId = Auth::user()->sekolah_id;

        if (!$sekolahId) {
            return redirect()->back()->with('error', 'Anda tidak terhubung dengan sekolah manapun.');
        }

        // Ambil daftar kursus yang dimiliki oleh sekolah tersebut
        $courses = Course::where('sekolah_id', $sekolahId)
            ->orderBy('nama_kelas')
            ->get();

        $query = Attendance::with([
            'user.role',
            'user.sekolah',
            'user.kelas',
            'course.jenjang',
            'activity'
        ]);

        // Filter utama: Absensi harus terkait dengan kursus di sekolah ini atau kegiatan yang diikuti user dari sekolah ini
        $query->where(function ($q) use ($sekolahId) {
            $q->whereHas('course', function ($qCourse) use ($sekolahId) {
                $qCourse->where('sekolah_id', $sekolahId);
            })->orWhereHas('user', function ($qUser) use ($sekolahId) {
                // Asumsi absensi kegiatan tidak langsung punya course_id, tapi user-nya dari sekolah ini
                $qUser->where('sekolah_id', $sekolahId);
            });
        });


        // Filter tambahan (sesuai yang ada di view Anda)
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

        // --- Logika Export Excel ---
        if ($request->has('export_excel') && $request->input('export_excel') == '1') {
            // Ambil semua data absensi yang sudah difilter tanpa pagination
            $attendancesToExport = $query->latest()->get();

            // Tentukan nama file Excel
            // Menambahkan tanggal dan waktu export ke nama file
            $exportDate = Carbon::now()->format('Ymd_His');
            $schoolNameSlug = \Illuminate\Support\Str::slug(Auth::user()->sekolah->nama_sekolah ?? 'Tanpa_Sekolah');
            $fileName = "Laporan_Absensi_Sekolah_{$schoolNameSlug}_{$exportDate}.xlsx";

            // Lakukan export menggunakan Export Class baru
            return Excel::download(new SchoolCourseAttendanceExport($attendancesToExport), $fileName);
        }
        // --- Akhir Logika Export Excel ---

        // Jika bukan permintaan export, tampilkan data dengan pagination
        $attendances = $query->latest()->paginate(10);
        $attendances->appends($request->all());

        return view('attendances.sekolah.index', compact('attendances', 'courses'));
    }

    // ... method show() Anda tetap sama ...
    public function show(Attendance $attendance)
    {
        // Pastikan relasi user dan course dimuat
        $attendance->load(['user.role', 'course', 'activity']);

        $sekolahId = Auth::user()->sekolah_id; // Menggunakan sekolah_id langsung dari user

        // Cek apakah absensi adalah kursus DAN kursus tersebut milik sekolah user yang login
        // ATAU cek apakah absensi adalah kegiatan DAN kegiatan tersebut terkait dengan sekolah user yang login
        $hasAccess = false;
        if ($attendance->course_id && $attendance->course->sekolah_id === $sekolahId) {
            $hasAccess = true;
        } elseif ($attendance->activity_id && $attendance->user->school_id === $sekolahId) {
             // Asumsi activity tidak punya school_id, tapi user yang terdaftar di activity punya school_id
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->route('attendances.sekolah.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat data absensi ini.');
        }

        return view('attendances.sekolah.show', compact('attendance'));
    }
}