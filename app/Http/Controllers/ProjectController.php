<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $projectsQuery = Project::with('user', 'course')->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
    
            // Pencarian berdasarkan nama peserta
            $projectsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
    
            // Pencarian berdasarkan nama kursus
            $projectsQuery->orWhereHas('course', function ($query) use ($search) {
                $query->where('nama_kursus', 'like', "%{$search}%");
            });
        }

        if ($user->role_id == 3) { // Peserta
            // Peserta hanya bisa melihat project mereka sendiri
            $projects = Project::where('user_id', $user->id)
                        ->latest()
                        ->paginate(10);
            return view('projects.peserta.index', compact('projects'));

        } elseif ($user->role_id == 1) { // Admin
            // Admin bisa melihat semua project
            $projects = Project::with('user', 'course')
                        ->latest()
                        ->paginate(10);
            return view('projects.admin.index', compact('projects'));

        } elseif ($user->role_id == 2) { // Mentor
            // Mentor bisa melihat semua project, bisa ditambahkan filter berdasarkan course
            $projects = Project::with('user', 'course')
                        ->where('course_id', $request->course_id ?? null)
                        ->latest()
                        ->paginate(10);
            return view('projects.mentor.index', compact('projects'));

        } elseif ($user->role_id == 4) { // Karyawan
            // Karyawan hanya bisa melihat semua project, tidak bisa melakukan aksi lain
            $projects = Project::with('user', 'course')
                        ->latest()
                        ->paginate(10);
            return view('projects.karyawan.index', compact('projects'));

        } elseif ($user->role_id == 5) { // Vendor
            // Vendor hanya bisa melihat project, bisa ditambahkan fitur spesifik jika diperlukan
            $projects = Project::with('user', 'course')
                        ->latest()
                        ->paginate(10);
            return view('projects.vendor.index', compact('projects'));
        }

        abort(403); // Untuk role yang tidak dikenali
    }

    public function create()
    {
        $courses = Course::all(); // Peserta memilih kursus untuk membuat project
        return view('projects.peserta.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:255',
            'html_code' => 'nullable',
            'css_code' => 'nullable',
            'js_code' => 'nullable',
        ]);

        Project::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'title' => $request->title,
            'html_code' => $request->html_code,
            'css_code' => $request->css_code,
            'js_code' => $request->js_code,
        ]);

        return redirect()->route('projects.peserta.index')->with('success', 'Project berhasil dibuat!');
    }

    public function createCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        return view('projects.peserta.createproject', compact('course'));
    }

    public function storeCourse(Request $request, $courseId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'html_code' => 'nullable|string',
            'css_code' => 'nullable|string',
            'js_code' => 'nullable|string',
        ]);

        $project = new Project();
        $project->user_id = Auth::id();
        $project->course_id = $courseId;
        $project->title = $request->title;
        $project->html_code = $request->html_code;
        $project->css_code = $request->css_code;
        $project->js_code = $request->js_code;
        $project->save();

        return redirect()->route('courses.showPeserta', ['course' => $courseId])->with('success', 'Project berhasil dibuat.');
    }

    public function showPeserta(Project $project)
    {
        $user = Auth::user();

        // Peserta hanya bisa melihat project mereka sendiri
        if ($user->role_id == 3 && $project->user_id != $user->id) {
            abort(403);
        }

        return view('projects.peserta.show', compact('project'));
    }

    public function showCourse(Project $project)
    {
        $user = Auth::user();

        // Hanya admin, mentor dari course, atau peserta pemilik project yang boleh mengakses
        $isAdmin = $user->role_id == 1;
        $isMentorCourse = $user->role_id == 4 && $project->course && $project->course->mentor_id == $user->id;
        $isOwnerPeserta = $user->role_id == 3 && $project->user_id == $user->id;

        if (!($isAdmin || $isMentorCourse || $isOwnerPeserta)) {
            abort(403);
        }

        return view('course.showproject', compact('project'));
    }

    public function showAdmin(Project $project)
    {
        $user = Auth::user();

        // Admin bisa melihat project siapapun
        if ($user->role_id == 1) {
            return view('projects.admin.show', compact('project'));
        }

        abort(403);
    }

    public function showMentor(Project $project)
    {
        $user = Auth::user();

        // Mentor bisa melihat project siapapun
        if ($user->role_id == 2) {
            return view('projects.mentor.show', compact('project'));
        }

        abort(403);
    }

    public function showKaryawan(Project $project)
    {
        $user = Auth::user();

        // Karyawan bisa melihat project siapapun
        if ($user->role_id == 4) {
            return view('projects.karyawan.show', compact('project'));
        }

        abort(403);
    }


    public function edit(Project $project)
    {
        $user = Auth::user();

        // Peserta hanya bisa mengedit project mereka sendiri
        if ($user->role_id != 3 || $project->user_id != $user->id) {
            abort(403);
        }

        $courses = Course::all();
        return view('projects.peserta.edit', compact('project', 'courses'));
    }

    public function update(Request $request, Project $project)
    {
        $user = Auth::user();

        if ($user->role_id != 3 || $project->user_id != $user->id) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:255',
            'html_code' => 'nullable',
            'css_code' => 'nullable',
            'js_code' => 'nullable',
        ]);

        $project->update($request->only('course_id', 'title', 'html_code', 'css_code', 'js_code'));

        return redirect()->route('projects.peserta.index')->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $user = Auth::user();

        // Peserta hanya bisa menghapus project mereka sendiri
        if ($user->role_id != 3 || $project->user_id != $user->id) {
            abort(403);
        }

        $project->delete();

        return redirect()->route('projects.peserta.index')->with('success', 'Project berhasil dihapus!');
    }
}
