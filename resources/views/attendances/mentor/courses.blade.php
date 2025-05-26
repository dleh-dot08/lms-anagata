@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-gradient-purple text-white rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-white bg-opacity-25 rounded-circle p-3 me-3 shadow-sm">
                            <i class="bi bi-journal-richtext fs-3 text-white"></i> {{-- Changed icon to something more descriptive for courses --}}
                        </div>
                        <div>
                            <h2 class="h3 mb-1 text-white fw-bold">Daftar Kursus Anda</h2>
                            <p class="mb-0 text-white-75">Kelola dan akses detail setiap kelas yang Anda ajar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom-0 py-3 px-4">
                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Daftar Kelas yang Diajar</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0"> {{-- Removed table-bordered, added table-hover --}}
                            <thead class="bg-light"> {{-- Changed table-primary to bg-light for subtler header --}}
                                <tr>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">No</th>
                                    <th class="px-4 py-3 fw-semibold text-dark">Nama Kelas</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Kategori</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Jenjang</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Pertemuan</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Siswa</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Status</th>
                                    <th class="px-4 py-3 fw-semibold text-dark text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courses as $index => $course)
                                    <tr class="course-row {{ $index % 2 == 0 ? 'bg-white' : 'bg-light bg-opacity-25' }}"> {{-- Added custom class and alternating background --}}
                                        <td class="text-center px-4 py-3">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 fw-bold text-primary">{{ $course->nama_kelas }}</td> {{-- Emphasize class name --}}
                                        <td class="px-4 py-3 text-center">{{ $course->kategori->nama_kategori }}</td>
                                        <td class="px-4 py-3 text-center">{{ $course->jenjang->nama_jenjang }}</td>
                                        <td class="px-4 py-3 text-center">{{ $course->meetings->count() }}</td>
                                        <td class="px-4 py-3 text-center">{{ $course->enrollments->count() }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if (now()->between($course->waktu_mulai, $course->waktu_akhir))
                                                <span class="badge bg-success rounded-pill py-2 px-3">Aktif</span> {{-- Added rounded-pill, padding --}}
                                            @else
                                                <span class="badge bg-secondary rounded-pill py-2 px-3">Tidak Aktif</span> {{-- Added rounded-pill, padding --}}
                                            @endif
                                        </td>
                                        <td class="text-center px-4 py-3">
                                            {{-- Group actions in a dropdown or use distinct buttons if few actions --}}
                                            <div class="d-flex justify-content-center">
                                                <a href="{{ route('mentor.attendances.select_meeting', $course->id) }}"
                                                class="btn btn-primary btn-sm rounded-pill shadow-sm me-2 d-flex align-items-center"> {{-- More prominent button --}}
                                                    <i class="bi bi-calendar-check me-1"></i>Absen
                                                </a>
                                                <a href="{{ route('kursus.mentor.overview', $course->id) }}"
                                                class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm d-flex align-items-center"> {{-- Secondary button for details --}}
                                                    <i class="bi bi-info-circle me-1"></i>Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <span class="text-muted fs-5"><i class="bi bi-exclamation-circle me-2"></i>Anda belum memiliki kursus yang diajar saat ini.</span>
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
@endsection

<style>
/* Define a custom color palette (consistent with previous improvements) */
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

/* General Styling */
.bg-gradient-purple {
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%);
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.rounded-3 {
    border-radius: 0.75rem !important;
}

.shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
}

.icon-shape {
    transition: all 0.3s ease;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15)!important;
}

.card {
    transition: all 0.3s ease;
    border-radius: 0.75rem;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
}

/* Table Specific Styling */
.table {
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--dark);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--purple-lighter); /* Subtle line */
}

.table tbody td {
    border-top: 1px solid rgba(0,0,0,.05); /* Light border between rows */
    vertical-align: middle;
    padding: 0.75rem 1rem; /* Consistent padding */
}

.table tbody tr:first-child td {
    border-top: none; /* No top border for the very first row */
}

/* Custom row hover effect */
.course-row:hover {
    background-color: var(--purple-lighter) !important; /* Light purple on hover */
    transition: background-color 0.2s ease-in-out;
}

/* Badge Styling */
.badge {
    font-weight: 600;
    padding: 0.6em 1em;
    border-radius: 0.5rem; /* More rounded badges */
    font-size: 0.75rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Button Styling */
.btn.rounded-pill {
    border-radius: 50rem !important; /* Extremely rounded */
}

.btn-primary {
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%) !important;
    border-color: var(--purple-main) !important;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    background: linear-gradient(135deg, var(--purple-dark) 0%, var(--purple-main) 100%) !important;
    border-color: var(--purple-dark) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

.btn-outline-secondary {
    border-color: var(--secondary) !important;
    color: var(--secondary) !important;
    transition: all 0.3s ease;
}
.btn-outline-secondary:hover {
    background-color: var(--secondary) !important;
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}


/* Responsiveness */
@media (max-width: 767.98px) {
    .p-4 {
        padding: 1.5rem !important;
    }
    .fs-3 {
        font-size: 1.75rem !important;
    }
    .h3 {
        font-size: 1.5rem !important;
    }
    .table-responsive {
        font-size: 0.85rem;
    }
    .table th, .table td {
        padding: 0.5rem 0.75rem;
        white-space: nowrap; /* Keep content on one line if possible */
    }
    .d-flex.justify-content-center {
        flex-direction: column; /* Stack buttons vertically on small screens */
        align-items: center;
    }
    .d-flex.justify-content-center .btn {
        width: 100%;
        margin-bottom: 0.5rem; /* Space between stacked buttons */
        margin-right: 0 !important; /* Remove right margin if stacked */
    }
}
</style>