@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-gradient-purple text-white rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-blue bg-opacity-25 rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-calendar-check-fill fs-3 text-white"></i>
                        </div>
                        <div>
                            <h2 class="h3 mb-1 text-white fw-bold">Laporan Absensi Mengajar Saya</h2>
                            <p class="mb-0 text-white-75">Ringkasan kehadiran Anda di setiap pertemuan kelas yang Anda ajar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('mentor.self.attendance') }}" method="GET" class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div class="flex-grow-1 me-md-3 mb-3 mb-md-0">
                            <label for="course_filter" class="form-label mb-1 fw-semibold text-muted">Filter Berdasarkan Kelas:</label>
                            <select class="form-select form-select-lg shadow-sm" id="course_filter" name="course_id" onchange="this.form.submit()">
                                <option value="">--- Semua Kelas ---</option>
                                @foreach ($mentorCourses as $course)
                                    <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                                        {{ $course->nama_kelas }} ({{ $course->kode_unik }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary d-none d-md-block btn-lg shadow-sm">
                            <i class="bi bi-filter me-2"></i>Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 py-3 px-4">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-list-columns-reverse me-2 text-primary"></i>Detail Absensi Mengajar</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 fw-semibold text-dark text-nowrap" style="min-width: 250px;">Kelas</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center text-nowrap">Pertemuan Ke-</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center text-nowrap">Tanggal Pertemuan</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center text-nowrap">Status Absensi</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-nowrap">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groupedAbsensi as $courseName => $absensiList)
                                    <tr class="table-group-divider bg-purple-lightest">
                                        <td colspan="5" class="px-4 py-3 fw-bold text-purple-dark fs-5">{{ $courseName }}</td>
                                    </tr>
                                    @foreach ($absensiList as $index => $absen)
                                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-light bg-opacity-25' }} hover-row">
                                            <td class="px-4 py-3">{{ $absen->meeting->course->nama_kelas }}</td>
                                            <td class="px-4 py-3 text-center">{{ $absen->meeting->pertemuan }}</td>
                                            <td class="px-4 py-3 text-center">
                                                {{ \Carbon\Carbon::parse($absen->attendance_date)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $badgeClass = '';
                                                    switch($absen->status) {
                                                        case 'Hadir': $badgeClass = 'bg-success'; break;
                                                        case 'Tidak Hadir': $badgeClass = 'bg-danger'; break;
                                                        case 'Izin': $badgeClass = 'bg-warning'; break;
                                                        case 'Sakit': $badgeClass = 'bg-info'; break;
                                                        default: $badgeClass = 'bg-secondary'; break;
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }} py-2 px-3">{{ $absen->status }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-muted small">{{ $absen->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fs-5">
                                            <i class="bi bi-exclamation-circle-fill me-2"></i>Belum ada data absensi mengajar untuk kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Define a custom color palette */
:root {
    --purple-main: #6f42c1; /* A primary purple */
    --purple-dark: #5e35b1; /* Darker purple for accents */
    --purple-accent: #7b1fa2; /* A more vibrant purple */
    --purple-light: #9c27b0; /* Lighter purple */
    --purple-lighter: #e1bee7; /* Very light purple */
    --purple-lightest: #f3e5f5; /* Almost white purple for table group headers */

    --primary: #0d6efd; /* Bootstrap primary blue */
    --secondary: #6c757d;
    --success: #198754;
    --info: #0dcaf0;
    --warning: #ffc107;
    --danger: #dc3545;
    --light: #f8f9fa;
    --dark: #212529;
}

.bg-gradient-purple {
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%);
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.rounded-3 {
    border-radius: 0.75rem !important; /* Consistent border radius */
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

.icon-shape {
    transition: all 0.3s ease;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important; /* Deeper shadow for icons */
}

.card {
    transition: all 0.3s ease;
    border-radius: 0.75rem; /* Match rounded-3 */
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
}

.form-select-lg {
    height: calc(3.5rem + 2px); /* Slightly larger for better touch on mobile */
    padding: 0.75rem 1.75rem 0.75rem 1rem;
    font-size: 1.25rem;
}

.table {
    border-collapse: separate; /* Required for rounded corners on inner table */
    border-spacing: 0;
}

.table th, .table td {
    border: none; /* Remove default table borders */
    vertical-align: middle;
}

.table thead th {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--dark);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--purple-lighter); /* Subtle line */
}

.table tbody tr:last-child td {
    border-bottom: none; /* No border for the last row */
}

/* Custom row hover effect */
.hover-row:hover {
    background-color: var(--purple-lighter) !important;
    transition: background-color 0.2s ease-in-out;
}

/* Specific styling for table group dividers */
.table-group-divider {
    background-color: var(--purple-lightest) !important;
    border-top: 1px solid var(--purple-lighter) !important;
    border-bottom: 1px solid var(--purple-lighter) !important;
}

.text-purple-dark {
    color: var(--purple-dark) !important;
}

.badge {
    font-weight: 600; /* Bolder badges */
    padding: 0.6em 1em; /* Larger padding for badges */
    border-radius: 0.5rem; /* More rounded badges */
    font-size: 0.7rem;
    line-height: 1; /* Ensure text sits well */
    display: inline-flex; /* For better icon alignment if used */
    align-items: center;
    justify-content: center;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .fs-3 { /* Adjust font size for small screens */
        font-size: 1.75rem !important;
    }
    .h3 {
        font-size: 1.5rem !important;
    }
    .p-4 {
        padding: 1.5rem !important;
    }
    .form-select-lg {
        font-size: 1rem;
        height: auto;
        padding: 0.5rem 1rem;
    }
    .table-responsive {
        font-size: 0.85rem;
    }
    .table th, .table td {
        padding: 0.5rem 0.75rem;
    }
    .text-nowrap { /* Prevent wrapping on small screens for table headers */
        white-space: nowrap;
    }
}
</style>
@endsection