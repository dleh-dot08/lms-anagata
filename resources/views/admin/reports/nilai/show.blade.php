@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Laporan / Nilai /</span> {{ $course->nama_kelas }}
        </h4>
    </div>
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <a href="{{ route('admin.reports.nilai.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            {{-- Tombol Edit Nilai perlu disesuaikan jika Anda ingin admin bisa edit nilai --}}
            <a href="{{ route('admin.reports.nilai.edit', $course->id) }}" class="btn btn-warning">
                <i class="bx bx-edit me-1"></i> Edit Nilai
            </a>
        </div>
        <div>
            <a href="{{ route('admin.reports.nilai.export-pdf', $course->id) }}" class="btn btn-danger">
                <i class="bx bx-file me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.nilai.export', $course->id) }}" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Unik:</strong> {{ $course->kode_unik ?? 'Tidak Ada' }}</p>
                    <p><strong>Mentor:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
                    <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</p>
                    <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</p>
                    <p><strong>Kelas:</strong> {{ $course->kelas->nama ?? 'Tidak Ada' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Sekolah:</strong> {{ $course->sekolah->nama_sekolah ?? 'Tidak Ada' }}</p>
                    <p><strong>Program:</strong> {{ $course->program->nama_program ?? 'Tidak Ada' }}</p>
                    <p><strong>Level:</strong> {{ $course->level }}</p>
                    <p><strong>Deskripsi:</strong> {{ $course->deskripsi }}</p>
                    <p><strong>Status:</strong> {{ $course->status }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <style>
                    .table-wrapper {
                        position: relative;
                        overflow: auto;
                        background: white;
                        height: 600px;
                        border: 1px solid #dee2e6;
                        border-radius: 0.375rem;
                    }
                    /* Header container */
                    thead {
                        position: sticky;
                        top: 0;
                        z-index: 3;
                        background: #fcfdfd;
                    }
                    /* Sticky columns */
                    .sticky-col {
                        position: sticky;
                        background: white !important;
                        z-index: 2;
                    }
                    .first-col {
                        left: 0;
                        min-width: 80px;
                        width: 80px;
                    }
                    .second-col {
                        left: 80px;
                        min-width: 220px;
                        width: 220px;
                    }
                    .third-col {
                        left: 300px;
                        min-width: 120px;
                        width: 120px;
                    }
                    /* Header cells */
                    thead th {
                        background: #fcfdfd !important;
                    }
                    /* Sticky header cells */
                    thead .sticky-col {
                        z-index: 4;
                        background: #fcfdfd !important;
                    }
                    /* Borders */
                    .sticky-col {
                        border-right: 1px solid #dee2e6;
                    }
                    .third-col {
                        border-right: 2px solid #dee2e6;
                    }
                    .second-col, .third-col {
                        border-left: none;
                    }
                    /* Shadow for sticky columns */
                    .third-col::after {
                        content: '';
                        position: absolute;
                        top: 0;
                        right: -2px;
                        bottom: 0;
                        width: 4px;
                        pointer-events: none;
                        box-shadow: inset -2px 0 2px -2px rgba(0,0,0,0.1);
                    }
                    /* Table body cells */
                    tbody td {
                        background: white !important;
                    }
                    /* Add padding after frozen columns */
                    .table td:nth-child(4),
                    .table th:nth-child(4) {
                        padding-left: 20px;
                    }
                </style>
                <div class="table-wrapper">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="text-center align-middle sticky-col first-col">No</th>
                                <th rowspan="2" class="text-center align-middle sticky-col second-col">Nama Siswa</th>
                                <th rowspan="2" class="text-center align-middle sticky-col third-col">Kelas</th>
                                @foreach($course->meetings as $meeting)
                                <th colspan="4" class="text-center">
                                    Pertemuan {{ $loop->iteration }}
                                    <div class="small text-muted">{{ $meeting->tanggal_pelaksanaan ? \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->format('d/m/Y') : '-' }}</div>
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($course->meetings as $meeting)
                                <th class="text-center">Creativity</th>
                                <th class="text-center">Program</th>
                                <th class="text-center">Design</th>
                                <th class="text-center">Total<div class="small text-muted">oleh Mentor</div></th> {{-- Tambah label mentor di sini --}}
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->participants as $index => $participant)
                            <tr>
                                <td class="text-center sticky-col first-col">{{ $index + 1 }}</td>
                                <td class="sticky-col second-col">{{ $participant->name }}</td>
                                <td class="text-center sticky-col third-col">
                                    {{ $participant->kelas ? $participant->kelas->nama_kelas : '-' }}
                                </td>
                                @foreach($course->meetings as $meeting)
                                    @php
                                        // Cari skor dari relasi yang sudah di-eager load
                                        $score = $participant->scores->where('meeting_id', $meeting->id)->first();
                                        $mentorName = ($score && $score->mentor) ? $score->mentor->name : 'N/A';
                                    @endphp
                                    <td class="text-center">{{ ($score && $score->creativity_score !== null) ? number_format($score->creativity_score, 1) : '-' }}</td>
                                    <td class="text-center">{{ ($score && $score->program_score !== null) ? number_format($score->program_score, 1) : '-' }}</td>
                                    <td class="text-center">{{ ($score && $score->design_score !== null) ? number_format($score->design_score, 1) : '-' }}</td>
                                    <td class="text-center">
                                        {{ ($score && $score->total_score !== null) ? number_format($score->total_score, 1) : '-' }}
                                        <div class="small text-muted" style="font-size: 0.75em;">({{ $mentorName }})</div> {{-- Tampilkan nama mentor di sini --}}
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection