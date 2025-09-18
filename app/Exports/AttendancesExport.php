<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\sheets\AllAttendancesSheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\sheets\CourseAttendancesSheetExport;
use App\Exports\sheets\SchoolAttendancesSheetExport;
use App\Exports\sheets\MentorAttendancesSheetExport;

class AttendancesExport implements WithMultipleSheets
{
    use Exportable;

    protected $exportType;
    protected $selectedCourseId;
    protected $selectedSchoolId;
    protected $startDate;
    protected $endDate;

    public function __construct($exportType, $selectedCourseId = null, $selectedSchoolId = null, $startDate = null, $endDate = null)
    {
        $this->exportType = $exportType;
        $this->selectedCourseId = $selectedCourseId;
        $this->selectedSchoolId = $selectedSchoolId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Untuk contoh, kita akan membuat sub-export class.
        // Anda bisa membuat satu sub-export class generik atau spesifik
        // contoh di sini saya akan buat CourseAttendancesSheetExport
        // dan SchoolAttendancesSheetExport

        if ($this->exportType === 'all_courses') {
            $courses = \App\Models\Course::all(); // Ambil semua kursus
            foreach ($courses as $course) {
                $sheets[] = new CourseAttendancesSheetExport($course->id, $this->startDate, $this->endDate);
                $sheets[] = new MentorAttendancesSheetExport($this->selectedSchoolId, $this->startDate, $this->endDate); // Tambah sheet mentor
            }
        } elseif ($this->exportType === 'selected_course' && $this->selectedCourseId) {
            $sheets[] = new CourseAttendancesSheetExport($this->selectedCourseId, $this->startDate, $this->endDate);
        } elseif ($this->exportType === 'all_schools') {
            $schools = \App\Models\Sekolah::all(); // Ambil semua sekolah
            foreach ($schools as $school) {
                $sheets[] = new SchoolAttendancesSheetExport($school->id, $this->startDate, $this->endDate);
                $sheets[] = new MentorAttendancesSheetExport($school->id, $this->startDate, $this->endDate); // Tambah sheet mentor
            }
        } elseif ($this->exportType === 'selected_school' && $this->selectedSchoolId) {
            $sheets[] = new SchoolAttendancesSheetExport($this->selectedSchoolId, $this->startDate, $this->endDate);
        } else {
            // Default: export semua absensi ke satu sheet jika tidak ada filter
            $sheets[] = new AllAttendancesSheetExport($this->startDate, $this->endDate);
        }

        return $sheets;
    }
}