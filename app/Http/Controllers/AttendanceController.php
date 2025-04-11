<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ADMIN - Lihat semua absensi dengan filter tanggal
    public function adminIndex(Request $request)
    {
        $query = Attendance::with(['user', 'course'])->latest('tanggal');

        if ($request->has('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $attendances = $query->paginate(20);
        return view('attendances.admin.index', compact('attendances'));
    }

    // ADMIN - Show detail absensi
    public function show(Attendance $attendance)
    {
        return view('attendances.admin.show', compact('attendance'));
    }

    // PESERTA / MENTOR - Form absensi
    public function create(Course $course)
    {
        $today = Carbon::today()->toDateString();
        $already = Attendance::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($already) {
            return redirect()->back()->with('info', 'Kamu sudah absen hari ini.');
        }

        return view('attendances.' . (Auth::user()->role_id == 2 ? 'mentor' : 'peserta') . '.create', compact('course'));
    }

    // PESERTA / MENTOR - Store absensi
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'status' => 'required|in:Hadir,Tidak Hadir,Izin,Sakit',
            'file_attache' => 'nullable|file|max:2048',
            'ttd_digital' => 'nullable|file|max:2048',
        ]);

        $today = Carbon::today()->toDateString();
        $user_id = Auth::id();

        $sudah = Attendance::where('user_id', $user_id)
            ->where('course_id', $request->course_id)
            ->whereDate('tanggal', $today)
            ->exists();

        if ($sudah) {
            return redirect()->back()->with('error', 'Sudah absen hari ini.');
        }

        $file = $request->file('file_attache')?->store('absensi/foto');
        $ttd = $request->file('ttd_digital')?->store('absensi/ttd');

        Attendance::create([
            'user_id' => $user_id,
            'course_id' => $request->course_id,
            'tanggal' => $today,
            'waktu_absen' => now(),
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'file_attache' => $file,
            'ttd_digital' => $ttd,
            'status' => $request->status,
        ]);

        return redirect()->route('attendances.rekap')->with('success', 'Absensi berhasil disimpan.');
    }

    // PESERTA / MENTOR - Rekap pribadi
    public function rekap()
    {
        $attendances = Attendance::with('course')
            ->where('user_id', Auth::id())
            ->orderByDesc('tanggal')
            ->paginate(20);

        $folder = Auth::user()->role_id == 2 ? 'mentor' : 'peserta';
        return view("attendances.$folder.rekap", compact('attendances'));
    }
}
