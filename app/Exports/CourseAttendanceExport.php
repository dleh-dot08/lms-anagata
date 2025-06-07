<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CourseAttendanceExport implements WithMultipleSheets
{
    protected $attendances;

    public function __construct(Collection $attendances)
    {
        $this->attendances = $attendances;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet untuk Absensi Mentor (role_id 2)
        $mentorAttendances = $this->attendances->filter(function ($attendance) {
            return $attendance->user && $attendance->user->role_id == 2;
        });
        $sheets[] = new AttendanceSheet($mentorAttendances, 'Absensi Mentor');

        // Sheet untuk Absensi Siswa/Peserta (role_id 3)
        $participantAttendances = $this->attendances->filter(function ($attendance) {
            return $attendance->user && $attendance->user->role_id == 3;
        });
        $sheets[] = new AttendanceSheet($participantAttendances, 'Absensi Siswa');

        return $sheets;
    }
}