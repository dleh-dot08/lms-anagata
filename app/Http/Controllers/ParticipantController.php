<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Jenjang;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function form(Course $course, Request $request)
    {
        // Ambil peserta yang BELUM terdaftar di kursus ini
        $query = User::where('role_id', 3) // hanya peserta
            ->whereNotIn('id', function($sub) use ($course) {
                $sub->select('user_id')->from('enrollments')->where('course_id', $course->id);
            });

        // Filter jenjang jika ada
        if ($request->jenjang) {
            $query->where('jenjang_id', $request->jenjang);
        }

        // Filter pencarian (nama/email)
        if ($request->q) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        
        }

        // Pilihan per halaman: 10, 50, 100, all
        $perPage = $request->input('perPage', 10);
        if ($perPage === 'all') {
            $participants = $query->get();
        } else {
            $participants = $query->paginate((int)$perPage)->appends($request->all());
        }

        $jenjangList = Jenjang::all();

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.formparticipant', compact('course', 'participants', 'jenjangList', 'perPage'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.formparticipant', compact('course', 'participants', 'jenjangList', 'perPage'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $userIds = $request->input('user_ids', []);

        foreach ($userIds as $userId) {
            Enrollment::firstOrCreate([
                'course_id' => $course->id,
                'user_id' => $userId
            ], [
                'mentor_id' => $course->mentor_id,
                'tanggal_daftar' => now(),
                'tanggal_mulai' => now(),
            ]);
        }

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.show', $courseId)->with('success', 'Peserta berhasil ditambahkan!');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.show', $courseId)->with('success', 'Peserta berhasil ditambahkan!');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function destroy($courseId, $userId)
    {
        Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
