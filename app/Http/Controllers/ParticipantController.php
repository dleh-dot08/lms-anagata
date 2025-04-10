<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function form(Course $course)
    {
        $users = User::where('role_id', 3)->get(); // semua peserta

        return view('courses.formparticipant', compact('course', 'users'));
    }

    public function searchPeserta(Request $request)
    {
        $term = $request->input('q');
        $results = User::where('role_id', 3)
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orWhere('email', 'like', "%$term%");
            })
            ->limit(10)
            ->get();

        return response()->json($results->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => "{$user->name} ({$user->email})",
            ];
        }));
    }

    public function store(Request $request, Course $course)
    {
        $userIds = $request->input('user_ids', []);

        foreach ($userIds as $userId) {
            Enrollment::firstOrCreate([
                'course_id' => $course->id,
                'user_id' => $userId
            ], [
                'mentor_id' => $course->mentor_id,
                'tanggal_daftar' => now(),
                'tanggal_mulai' => now()
            ]);
        }

        return redirect()->route('courses.show', $course->id)->with('success', 'Peserta berhasil ditambahkan!');
    }
}

