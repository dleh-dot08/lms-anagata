<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\Sekolah; // Import model Sekolah
use App\Models\Kelas; 
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    public function form(Course $course, Request $request)
    {
        // Ambil peserta yang BELUM terdaftar di kursus ini
        $query = User::where('role_id', 3) // hanya peserta
            ->whereNotIn('id', function($sub) use ($course) {
                $sub->select('user_id')->from('enrollments')->where('course_id', $course->id);
            })
            ->with(['jenjang', 'sekolah', 'kelas']); // Eager load relasi untuk ditampilkan di tabel

        // Filter jenjang jika ada
        if ($request->filled('jenjang')) { // Gunakan filled() untuk memastikan nilai tidak kosong
            $query->where('jenjang_id', $request->jenjang);
        }

        // --- Tambahkan Filter Sekolah ---
        if ($request->filled('sekolah')) {
            $query->where('sekolah_id', $request->sekolah);
        }

        // --- Tambahkan Filter Kelas ---
        if ($request->filled('kelas')) {
            $query->where('kelas_id', $request->kelas);
        }

        // Filter pencarian (nama/email)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Pilihan per halaman: 10, 50, 100, all
        $perPage = $request->input('perPage', 10);
        if ($perPage === 'all') {
            $participants = $query->get();
        } else {
            $participants = $query->paginate((int)$perPage)->appends($request->all());
        }

        // Ambil daftar lengkap Jenjang, Sekolah, dan Kelas untuk dropdown filter
        $jenjangList = Jenjang::orderBy('nama_jenjang')->get();
        $sekolahList = Sekolah::orderBy('nama_sekolah')->get(); // Ambil semua sekolah
        $kelasList = Kelas::orderBy('nama')->get(); // Ambil semua kelas (sesuaikan 'nama' jika kolom nama kelas berbeda)

        $user = Auth::user();

        if ($user->role_id === 1) {
            return view('courses.formparticipant', compact('course', 'participants', 'jenjangList', 'sekolahList', 'kelasList', 'perPage')); // Kirim sekolahList dan kelasList
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.formparticipant', compact('course', 'participants', 'jenjangList', 'sekolahList', 'kelasList', 'perPage')); // Kirim sekolahList dan kelasList
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->input('user_ids', []);

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if ($user) {
                Enrollment::firstOrCreate([
                    'course_id' => $course->id,
                    'user_id' => $userId
                ], [
                    'mentor_id' => $course->mentor_id,
                    'tanggal_daftar' => now(),
                    'tanggal_mulai' => now(),
                    'tanggal_selesai' => null, // Biasanya ini null di awal
                    'status' => 'aktif', // <--- Set status awal ke 'aktif'
                    'jenjang_id' => $user->jenjang_id,
                    'sekolah_id' => $user->sekolah_id,
                    'kelas_id' => $user->kelas_id,
                ]);
            }
        }

        $loggedInUser = auth()->user();

        if ($loggedInUser->role_id === 1) {
            return redirect()->route('courses.show', $courseId)->with('success', 'Peserta berhasil ditambahkan!');
        } elseif ($loggedInUser->role_id === 4 && $loggedInUser->divisi === 'APD') {
            return redirect()->route('courses.apd.show', $courseId)->with('success', 'Peserta berhasil ditambahkan!');
        } else {
            abort(403, 'Akses dilarang. Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }
    }

    public function updateStatusParticipant(Request $request, Course $course, User $user)
    {
        // Validasi input status
        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif', // Pastikan nilai sesuai dengan ENUM di database
        ]);

        // Cari enrollment spesifik antara kursus dan user ini
        $enrollment = Enrollment::where('course_id', $course->id)
                                ->where('user_id', $user->id)
                                ->firstOrFail(); // Akan melempar 404 jika tidak ditemukan

        // Perbarui status enrollment
        $enrollment->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status peserta berhasil diperbarui.');
    }

    public function destroy($courseId, $userId)
    {
        Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
