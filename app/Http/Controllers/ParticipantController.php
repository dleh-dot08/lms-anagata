<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function show(Course $course)
    {
        $enrollments = Enrollment::where('course_id', $course->id)->with('user.jenjang')->get();
        $allPeserta = User::where('role_id', 3)->get();

        return view('participants.formparticipant', compact('course', 'enrollments', 'allPeserta'));
    }

    public function add(Request $request, Course $course)
    {
        $request->validate(['user_ids' => 'required|array']);
        foreach ($request->user_ids as $user_id) {
            Enrollment::firstOrCreate([
                'course_id' => $course->id,
                'user_id' => $user_id,
            ]);
        }

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function remove(Course $course, User $user)
    {
        Enrollment::where('course_id', $course->id)->where('user_id', $user->id)->delete();
        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $term = $request->q;
        $results = User::where('role_id', 3)
            ->where(function($query) use ($term) {
                $query->where('name', 'LIKE', "%$term%")
                      ->orWhere('email', 'LIKE', "%$term%");
            })
            ->limit(10)
            ->get();

        return response()->json($results->map(function ($user) {
            return ['id' => $user->id, 'text' => $user->name . ' - ' . $user->email];
        }));
    }
}
