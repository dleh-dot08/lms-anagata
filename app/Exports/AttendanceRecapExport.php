<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Meeting;
use App\Models\User; // Asumsi siswa juga User
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Opsional: untuk auto-size kolom

class AttendanceRecapExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $courseId;
    protected $meetings;
    protected $students;

    public function __construct($courseId)
    {
        $this->courseId = $courseId;
        // Kita akan ambil ulang data students, meetings, dan course di sini
        // agar data yang diexport sama persis dengan yang ditampilkan di halaman
        $course = Course::findOrFail($courseId);
        $this->meetings = Meeting::where('course_id', $courseId)->orderBy('tanggal_pelaksanaan')->get();
        $this->students = User::whereHas('enrollments', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->with(['attendances' => function ($query) {
            $query->with('recordedByMentor'); // Pastikan relasi ini juga di-load
        }])->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect();

        // Header statis untuk kolom total
        $fixedHeaders = [
            'Nama Siswa',
        ];

        // Tambahkan header dinamis untuk setiap pertemuan
        foreach ($this->meetings as $meeting) {
            $fixedHeaders[] = 'P' . $meeting->pertemuan . ' (' . date('d/m', strtotime($meeting->tanggal_pelaksanaan ?? now())) . ') - Status';
            $fixedHeaders[] = 'P' . $meeting->pertemuan . ' (' . date('d/m', strtotime($meeting->tanggal_pelaksanaan ?? now())) . ') - Mentor';
        }

        // Tambahkan header untuk total absensi
        $fixedHeaders[] = 'Total Hadir';
        $fixedHeaders[] = 'Total Tidak Hadir';
        $fixedHeaders[] = 'Total Izin';
        $fixedHeaders[] = 'Total Sakit';

        // Baris data
        foreach ($this->students as $student) {
            $rowData = [
                $student->name,
            ];

            foreach ($this->meetings as $meeting) {
                $attendance = $student->attendances->where('meeting_id', $meeting->id)->first();
                $status = $attendance ? $attendance->status : '-';
                $recordedByMentorName = $attendance && $attendance->recordedByMentor ? $attendance->recordedByMentor->name : 'N/A';

                $rowData[] = $status;
                $rowData[] = $recordedByMentorName;
            }

            // Hitung total absensi untuk siswa ini
            $hadir = $student->attendances->where('status', 'Hadir')->count();
            $tidakHadir = $student->attendances->where('status', 'Tidak Hadir')->count();
            $izin = $student->attendances->where('status', 'Izin')->count();
            $sakit = $student->attendances->where('status', 'Sakit')->count();

            $rowData[] = $hadir;
            $rowData[] = $tidakHadir;
            $rowData[] = $izin;
            $rowData[] = $sakit;

            $data->push($rowData);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Ini akan membuat baris header pertama di Excel
        $headers = ['Nama Siswa'];
        foreach ($this->meetings as $meeting) {
            $headers[] = 'Pertemuan ' . $meeting->pertemuan . ' (' . date('d/m', strtotime($meeting->tanggal_pelaksanaan ?? now())) . ')';
            $headers[] = ''; // Kolom kosong untuk mentor
        }
        $headers[] = 'Total Hadir';
        $headers[] = 'Total Tidak Hadir';
        $headers[] = 'Total Izin';
        $headers[] = 'Total Sakit';

        // Ini akan membuat baris header kedua di Excel
        $subHeaders = ['']; // Kosongkan untuk kolom Nama Siswa
        foreach ($this->meetings as $meeting) {
            $subHeaders[] = 'Status Absensi';
            $subHeaders[] = 'Mentor Perekam';
        }
        $subHeaders[] = ''; // Kosongkan untuk Total Hadir
        $subHeaders[] = ''; // Kosongkan untuk Total Tidak Hadir
        $subHeaders[] = ''; // Kosongkan untuk Total Izin
        $subHeaders[] = ''; // Kosongkan untuk Total Sakit

        return [$headers, $subHeaders];
    }

    /**
     * @var Attendance $attendance
     */
    public function map($row): array
    {
        // Karena collection() sudah mengembalikan array yang diformat,
        // method map ini bisa kita lewati atau gunakan untuk transformasi lebih lanjut jika diperlukan.
        // Dalam kasus ini, kita mengembalikan row apa adanya.
        return $row;
    }
}