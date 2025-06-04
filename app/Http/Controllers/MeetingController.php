<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon; // Import Carbon untuk manipulasi tanggal/waktu

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
            'jam_mulai' => 'required|date_format:H:i', // Validasi jam
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai', // Validasi jam
        ]);
    
        // Buat pertemuan baru
        $meeting = $course->meetings()->create([
            'judul' => $request->judul,
            'description' => $request->description,
            'pertemuan' => $request->pertemuan,
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'schedule_history' => [], // Inisialisasi history kosong saat membuat baru
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
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'reason' => 'nullable|string|max:500', // Validasi alasan perubahan
        ]);

        // Ambil data LAMA sebelum update
        $oldTanggalPelaksanaan = $meeting->tanggal_pelaksanaan->format('Y-m-d'); // Format ke string untuk perbandingan
        $oldJamMulai = $meeting->jam_mulai ? $meeting->jam_mulai->format('H:i') : null;
        $oldJamSelesai = $meeting->jam_selesai ? $meeting->jam_selesai->format('H:i') : null;
        
        // Data baru dari request
        $newTanggalPelaksanaan = $request->tanggal_pelaksanaan;
        $newJamMulai = $request->jam_mulai;
        $newJamSelesai = $request->jam_selesai;

        // Cek apakah ada perubahan pada jadwal (tanggal atau jam)
        $hasScheduleChanged = false;
        if ($oldTanggalPelaksanaan != $newTanggalPelaksanaan ||
            $oldJamMulai != $newJamMulai ||
            $oldJamSelesai != $newJamSelesai) {
            $hasScheduleChanged = true;
        }

        // Update data pertemuan
        $meeting->update($request->only('pertemuan', 'judul', 'description', 'tanggal_pelaksanaan', 'jam_mulai', 'jam_selesai'));

        // Jika ada perubahan jadwal, catat ke history
        if ($hasScheduleChanged) {
            $history = $meeting->schedule_history ?? []; // Ambil history yang sudah ada, atau array kosong
            
            $history[] = [
                'old_tanggal_pelaksanaan' => $oldTanggalPelaksanaan,
                'old_jam_mulai' => $oldJamMulai,
                'old_jam_selesai' => $oldJamSelesai,
                'new_tanggal_pelaksanaan' => $newTanggalPelaksanaan,
                'new_jam_mulai' => $newJamMulai,
                'new_jam_selesai' => $newJamSelesai,
                'changed_by_user_id' => auth()->id(), // ID user yang sedang login
                'changed_by_user_name' => auth()->user()->name ?? 'Guest', // Nama user
                'reason' => $request->input('reason'),
                'changed_at' => Carbon::now()->toDateTimeString(), // Waktu perubahan
            ];

            $meeting->schedule_history = $history; // Simpan kembali array history yang sudah diupdate
            $meeting->save(); // Simpan perubahan pada kolom history
        }
    
        return redirect()->route('courses.show', $course->id)->with('success', 'Pertemuan berhasil diperbarui.');
    }
    
    public function destroy(Course $course, Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('courses.show', $course->id)->with('success', 'Pertemuan berhasil dihapus.');
    }

    // ... (metode convertToEmbed dan show lainnya) ...

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

        // Eager load scheduleChanges jika Anda ingin menampilkannya di halaman show ini
        $meeting = Meeting::with('lesson', 'schedule_history') // Tidak perlu eager load history karena sudah di meeting object
                        ->where('id', $meetingId)
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