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

    private function convertToEmbed($url)
    {
        if (!$url) return null;

        if (str_contains($url, 'youtube.com/watch')) {
            return str_replace('watch?v=', 'embed/', $url);
        }

        if (str_contains($url, 'drive.google.com')) {
            if (str_contains($url, '/view')) {
                return str_replace('/view', '/preview', $url);
            }

            if (str_contains($url, '/file/d/')) {
                return preg_replace('#/file/d/(.*?)/.*#', '/file/d/$1/preview', $url);
            }
        }

        return $url;
    }


    public function show($courseId, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        // Kalau kamu pakai convertToEmbed, bisa lakukan di sini:
        $lesson->video_url1 = $this->convertToEmbed($lesson->video_url1);
        $lesson->video_url2 = $this->convertToEmbed($lesson->video_url2);
        $lesson->video_url3 = $this->convertToEmbed($lesson->video_url3);

        $lesson->file_materi1 = $this->convertToEmbed($lesson->file_materi1);
        $lesson->file_materi2 = $this->convertToEmbed($lesson->file_materi2);
        $lesson->file_materi3 = $this->convertToEmbed($lesson->file_materi3);
        $lesson->file_materi4 = $this->convertToEmbed($lesson->file_materi4);
        $lesson->file_materi5 = $this->convertToEmbed($lesson->file_materi5);

        return view('courses.showlesson', compact('lesson'));
    }

}

