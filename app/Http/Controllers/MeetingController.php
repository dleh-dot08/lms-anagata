<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MeetingController extends Controller
{
    public function create(Course $course)
    {
        return view('courses.formmeetings', compact('course'));
    }
    
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'description' => 'required|string',
            'pertemuan' => 'required|integer',
            'tanggal_pelaksanaan' => 'required|date',
        ]);
    
        $course->meetings()->create([
            'judul' => $request->judul,
            'description' => $request->description,
            'pertemuan' => $request->pertemuan,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
        ]);
    
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Pertemuan berhasil ditambahkan.');
    }
    
    
    public function edit(Course $course, Meeting $meeting)
    {
        return view('courses.formmeetings', compact('course', 'meeting'));
    }
    
    public function update(Request $request, Course $course, Meeting $meeting)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'description' => 'required|string',
            'pertemuan' => 'required|integer',
            'tanggal_pelaksanaan' => 'required|date',
        ]);
    
        $meeting->update($request->only('pertemuan', 'judul', 'description', 'tanggal_pelaksanaan'));
    
        return redirect()->route('courses.show', $course->id)->with('success', 'Pertemuan berhasil diperbarui.');
    }
    
    public function destroy(Course $course, Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('courses.show', $course->id)->with('success', 'Pertemuan berhasil dihapus.');
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

    public function show($courseId, $meetingId)
    {
        $course = Course::findOrFail($courseId);

        $meeting = Meeting::with('lesson')->where('id', $meetingId)
                        ->where('course_id', $courseId)
                        ->firstOrFail();

        $lesson = $meeting->lesson;

        if ($lesson) {
            $lesson->video_url1 = $this->convertToEmbed($lesson->video_url1);
            $lesson->video_url2 = $this->convertToEmbed($lesson->video_url2);
            $lesson->video_url3 = $this->convertToEmbed($lesson->video_url3);

            $lesson->file_materi1 = $this->convertToEmbed($lesson->file_materi1);
            $lesson->file_materi2 = $this->convertToEmbed($lesson->file_materi2);
            $lesson->file_materi3 = $this->convertToEmbed($lesson->file_materi3);
            $lesson->file_materi4 = $this->convertToEmbed($lesson->file_materi4);
            $lesson->file_materi5 = $this->convertToEmbed($lesson->file_materi5);
        }

        return view('meetings.mentor.show', [
            'course' => $course,
            'meeting' => $meeting,
            'lesson' => $lesson,
        ]);
    }
    
}
