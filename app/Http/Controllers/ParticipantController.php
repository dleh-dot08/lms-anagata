<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Jenjang;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function form($courseId, Request $request)
    {
        $course = Course::findOrFail($courseId);
        $jenjangs = Jenjang::all();
        $filterJenjang = $request->jenjang_id;

        $users = User::where('role_id', 3) // peserta
            ->when($filterJenjang, function ($query, $filterJenjang) {
                $query->where('jenjang_id', $filterJenjang);
            })
            ->get();

        return view('courses.formparticipant', compact('users', 'course', 'jenjangs', 'filterJenjang'));
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

        return redirect()->route('courses.show', $courseId)->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function destroy($courseId, $userId)
    {
        Enrollment::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
