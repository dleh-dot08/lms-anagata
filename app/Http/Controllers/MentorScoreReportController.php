<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\User;
use App\Models\Meeting; // Pastikan model Meeting diimport
use App\Models\Course;  // Pastikan model Course diimport
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ScoreRecapExport; // Sesuaikan dengan nama eksport Anda
use Maatwebsite\Excel\Facades\Excel;

class MentorScoreReportController extends Controller
{
    // Method index ini akan tetap sama, untuk menampilkan daftar kelas
    public function index(Request $request)
    {
        $mentorId = auth()->id();
        $today = now();

        $courses = auth()->user()->taughtCourses()
        ->whereDate('waktu_mulai', '<=', $today)
        ->whereDate('waktu_akhir', '>=', $today)
        ->get();

        return view('laporan.nilai.mentor.index', compact('courses'));
    }

    /**
     * Menampilkan laporan nilai siswa untuk kelas tertentu dengan detail pertemuan.
     */
    public function showReport(Request $request, $course_id)
    {
        $course = Course::findOrFail($course_id);
        
        // Cek apakah user adalah mentor utama atau cadangan untuk kursus ini
        $user = auth()->user();
        $isMentor = $course->mentor_id == $user->id || 
                   $course->mentor_id_2 == $user->id || 
                   $course->mentor_id_3 == $user->id;

        if (!$isMentor) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }

        // Ambil semua pertemuan untuk kelas ini, diurutkan berdasarkan nomor pertemuan
        $meetings = $course->meetings()->orderBy('pertemuan')->get();

        // Mulai query untuk mengambil semua siswa yang terdaftar di kelas ini.
        // Kita akan menggunakan query builder untuk memungkinkan filter nama siswa.
        // Asumsi: Course has many/belongs to many Users (siswa) melalui tabel pivot 'enrollments'
        // Jika Anda menggunakan relasi students() di Course model yang mengembalikan User, maka ini benar.
        $enrolledStudentsQuery = $course->students(); 

        // --- Logika Filter Siswa Berdasarkan Nama ---
        if ($request->filled('student_name')) {
            $studentName = $request->input('student_name');
            $enrolledStudentsQuery->where('name', 'like', '%' . $studentName . '%');
        }

        // Ambil koleksi siswa yang sudah difilter
        $enrollments = $enrolledStudentsQuery->get(); // Ini adalah koleksi model User (siswa)

        // Dapatkan ID siswa yang sudah difilter
        $filteredStudentIds = $enrollments->pluck('id');

        // Mulai query untuk mengambil data nilai
        $scoresQuery = Score::query()
                            ->whereHas('meeting', function ($query) use ($course_id) {
                                $query->where('course_id', $course_id);
                            })
                            ->whereIn('peserta_id', $filteredStudentIds) // Filter skor hanya untuk siswa yang relevan
                            ->with(['meeting', 'peserta', 'mentor']); // <-- TAMBAHKAN 'mentor' DI SINI

        // --- Logika Filter Skor Berdasarkan Pertemuan ---
        if ($request->filled('meeting_id') && $request->input('meeting_id') !== '') {
            $meetingId = $request->input('meeting_id');
            $scoresQuery->where('meeting_id', $meetingId);
        }

        // Ambil semua skor setelah filter diterapkan
        $scores = $scoresQuery->get();

        // Mengorganisir data skor agar mudah diakses di view
        // Kunci: 'peserta_id-meeting_id' => [Score Object]
        $scoresData = $scores->groupBy(function($score) {
            return $score->peserta_id . '-' . $score->meeting_id;
        });

        // Menyiapkan data untuk grafik (rata-rata per pertemuan)
        $chartLabels = $meetings->pluck('pertemuan')->map(fn($p) => 'Pertemuan ' . $p)->toArray();
        $chartDataCreativity = [];
        $chartDataDesign = [];
        $chartDataProgramming = [];
        $chartDataAverage = [];

        foreach ($meetings as $meeting) {
            // Kita hanya ingin skor untuk pertemuan ini DAN siswa yang TERFILTER saat ini
            $meetingScoresForFilteredStudents = $scores->where('meeting_id', $meeting->id);

            $creativityScores = $meetingScoresForFilteredStudents->pluck('creativity_score')->filter()->avg();
            $designScores = $meetingScoresForFilteredStudents->pluck('design_score')->filter()->avg();
            $programScores = $meetingScoresForFilteredStudents->pluck('program_score')->filter()->avg();

            // Masukkan null jika tidak ada skor untuk pertemuan tersebut
            $chartDataCreativity[] = !is_null($creativityScores) ? round($creativityScores, 1) : null;
            $chartDataDesign[] = !is_null($designScores) ? round($designScores, 1) : null;
            $chartDataProgramming[] = !is_null($programScores) ? round($programScores, 1) : null;
            
            // Hitung rata-rata keseluruhan per pertemuan
            $avgPerMeetingValues = [];
            if (!is_null($creativityScores)) $avgPerMeetingValues[] = $creativityScores;
            if (!is_null($designScores)) $avgPerMeetingValues[] = $designScores;
            if (!is_null($programScores)) $avgPerMeetingValues[] = $programScores;

            $chartDataAverage[] = !empty($avgPerMeetingValues) ? round(array_sum($avgPerMeetingValues) / count($avgPerMeetingValues), 1) : null;
        }

        // Mengirimkan semua data yang diperlukan ke view
        return view('laporan.nilai.mentor.report', compact(
            'course',
            'meetings',
            'enrollments', // Ini sekarang adalah siswa yang sudah difilter
            'scoresData',
            'chartLabels',
            'chartDataCreativity',
            'chartDataDesign',
            'chartDataProgramming',
            'chartDataAverage'
        ));
    }

    /**
     * Menampilkan detail rapor untuk siswa individual.
     * Tidak ada perubahan signifikan di sini, kecuali route name disesuaikan
     */
    public function showStudentScore(User $student)
    {
        $scores = $student->scores()->with('meeting.course', 'mentor')->get(); // Load meeting dan course melalui meeting, dan mentor

        return view('laporan.nilai.mentor.student', compact('student', 'scores')); // Menggunakan view student.blade.php
    }

    /**
     * Export laporan nilai siswa ke Excel.
     * Perlu disesuaikan logikanya agar sesuai dengan format tabel baru
     */
    public function exportExcel(Course $course)
    {
        return Excel::download(new ScoreRecapExport($course), 'rekap-nilai-'.$course->nama_kelas.'.xlsx');
    }

    /**
     * Export laporan nilai siswa ke PDF.
     * Perlu disesuaikan logikanya agar sesuai dengan format tabel baru
     */
    public function exportPdf($course_id)
    {
        $course = Course::findOrFail($course_id);

        $meetings = $course->meetings()->orderBy('pertemuan')->get();
        $enrollments = $course->users;
        $scoresData = Score::whereHas('meeting', function ($query) use ($course_id) {
                            $query->where('course_id', $course_id);
                        })
                        ->with(['meeting', 'peserta'])
                        ->get()
                        ->groupBy(function($score) {
                            return $score->peserta_id . '-' . $score->meeting_id;
                        });

        // Load view PDF yang baru dengan data yang sama
        $pdf = PDF::loadView('laporan.nilai.mentor.recap-pdf', compact('course', 'meetings', 'enrollments', 'scoresData'));
        return $pdf->download("rekap_nilai_{$course->nama_kelas}.pdf");
    }
}