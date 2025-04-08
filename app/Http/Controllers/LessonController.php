<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;

class LessonController extends Controller
{
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        return view('courses.formlesson', compact('course'));
    }

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'judul' => 'required',
            'pertemuan_ke' => 'required|integer',
        ]);

        $data = $request->all();
        $data['course_id'] = $courseId;

        Lesson::create($data);
        return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil ditambahkan');
    }

    public function edit($courseId, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $course = Course::findOrFail($courseId);
        return view('courses.formlesson', compact('lesson', 'course'));
    }

    public function update(Request $request, $courseId, $lessonId)
    {
        $request->validate([
            'judul' => 'required',
            'pertemuan_ke' => 'required|integer',
        ]);

        $lesson = Lesson::findOrFail($lessonId);
        $lesson->update($request->all());

        return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil diubah');
    }

    public function destroy($courseId, $lessonId)
    {
        Lesson::destroy($lessonId);
        return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil dihapus');
    }
}

