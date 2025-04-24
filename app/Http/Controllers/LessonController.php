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
        
        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.formlesson', compact('course'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.formlesson', compact('course'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
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

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil ditambahkan');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.show', $courseId)->with('success', 'Materi berhasil ditambahkan');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function edit($courseId, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $course = Course::findOrFail($courseId);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.formlesson', compact('lesson', 'course'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.formlesson', compact('lesson', 'course'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function update(Request $request, $courseId, $lessonId)
    {
        $request->validate([
            'judul' => 'required',
            'pertemuan_ke' => 'required|integer',
        ]);

        $lesson = Lesson::findOrFail($lessonId);
        $lesson->update($request->all());

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil diubah');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.show', $courseId)->with('success', 'Materi berhasil diubah');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    public function destroy($courseId, $lessonId)
    {
        Lesson::destroy($lessonId);

        $user = auth()->user();

        if ($user->role_id === 1) {
            return redirect()->route('courses.show', $courseId)->with('success', 'Materi berhasil dihapus');
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return redirect()->route('courses.apd.show', $courseId)->with('success', 'Materi berhasil dihapus');
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

    private function convertToEmbed($url)
    {
        if (!$url) return null;

        // Handle youtube.com/watch?v=xxx
        if (str_contains($url, 'youtube.com/watch')) {
            return str_replace('watch?v=', 'embed/', $url);
        }
    
        // Handle youtu.be/xxx
        if (str_contains($url, 'youtu.be/')) {
            $videoId = substr(parse_url($url, PHP_URL_PATH), 1);
            return 'https://www.youtube.com/embed/' . $videoId;
        }
    
        // Handle Google Drive
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

        $user = auth()->user();

        if ($user->role_id === 1) {
            return view('courses.showlesson', compact('lesson'));
    
        } elseif ($user->role_id === 4 && $user->divisi === 'APD') {
            return view('layouts.karyawan.kursus.showlesson', compact('lesson'));
    
        } else {
            abort(403, 'Akses dilarang.');
        }
    }

}

