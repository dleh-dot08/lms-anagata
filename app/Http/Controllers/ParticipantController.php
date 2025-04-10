<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function create(Course $course)
    {
        return view('courses.formparticipant', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $request->user_id,
            'mentor_id' => $course->mentor_id, // atau dari login user
            'tanggal_daftar' => now(),
            'tanggal_mulai' => now(),
        ]);

        return redirect()->route('courses.show', $course->id)->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function search(Request $request)
    {
        $term = $request->q;

        $users = User::where('role_id', 4) // hanya peserta
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orWhere('email', 'like', "%$term%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')',
                ];
            });

        return response()->json($users);
    }

    public function destroy(Course $course, User $user)
    {
        Enrollment::where('course_id', $course->id)
                  ->where('user_id', $user->id)
                  ->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }
}
