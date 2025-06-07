@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">Laporan / Nilai /</span> <span class="text-primary">{{ $course->nama_kelas }}</span>
        </h4>
        <div class="d-flex gap-2 flex-wrap"> {{-- Flex-wrap agar tombol bisa pindah baris di layar kecil --}}
            <a href="{{ route('sekolah.reports.nilai.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
            <a href="{{ route('sekolah.reports.nilai.export-pdf', $course->id) }}" class="btn btn-danger">
                <i class="bx bxs-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('sekolah.reports.nilai.export', $course->id) }}" class="btn btn-success">
                <i class="bx bxs-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Course Details & Summary --}}
    <div class="row g-4 mb-4"> {{-- Gunakan g-4 untuk gap antar kolom --}}
        <div class="col-lg-6 col-md-12">
            <div class="card h-100 shadow-sm border-start border-primary border-3"> {{-- Card dengan border kiri --}}
                <div class="card-body">
                    <h5 class="card-title fw-bold text-primary mb-3">Detail Kursus: {{ $course->nama_kelas }}</h5>
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Kode Unik:</small>
                            <p class="fw-semibold mb-0">{{ $course->kode_unik ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Mentor Pengajar:</small>
                            <p class="fw-semibold mb-0">{{ $course->mentor->name ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Kategori Kursus:</small>
                            <p class="fw-semibold mb-0">{{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Jenjang:</small>
                            <p class="fw-semibold mb-0">{{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Kelas Sekolah:</small>
                            <p class="fw-semibold mb-0">{{ $course->kelas->nama ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Program Kursus:</small>
                            <p class="fw-semibold mb-0">{{ $course->program->nama_program ?? 'Tidak Ada' }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Level:</small>
                            <p class="fw-semibold mb-0">{{ $course->level }}</p>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <small class="text-muted d-block">Status Kursus:</small>
                            <p class="mb-0">
                                <span class="badge bg-{{ $course->status == 'Aktif' ? 'success' : 'secondary' }} rounded-pill px-3 py-2">
                                    {{ $course->status }}
                                </span>
                            </p>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted d-block">Deskripsi Kursus:</small>
                            <p class="mb-0 text-break">{{ $course->deskripsi ?: 'Tidak ada deskripsi.' }}</p> {{-- text-break untuk kata panjang --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card h-100 shadow-sm border-start border-success border-3"> {{-- Card dengan border kiri --}}
                <div class="card-body">
                    <h5 class="card-title fw-bold text-success mb-3">Ringkasan Laporan Nilai</h5>
                    <div class="row text-center">
                        <div class="col-4 mb-3">
                            <div class="bg-light rounded p-3">
                                <h4 class="fw-bold text-success mb-1">{{ $course->enrollments->count() }}</h4>
                                <small class="text-muted">Jumlah Siswa</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="bg-light rounded p-3">
                                @php
                                    $totalAllScores = 0;
                                    $scoredEntriesCount = 0;
                                    foreach ($course->enrollments as $enrollment) {
                                        foreach ($course->meetings as $meeting) {
                                            $score = App\Models\Score::where('peserta_id', $enrollment->user->id)
                                                ->where('meeting_id', $meeting->id)
                                                ->first();
                                            if ($score && $score->total_score !== null) {
                                                $totalAllScores += $score->total_score;
                                                $scoredEntriesCount++;
                                            }
                                        }
                                    }
                                    $averageClassScore = $scoredEntriesCount > 0 ? $totalAllScores / $scoredEntriesCount : 0;
                                @endphp
                                <h4 class="fw-bold text-info mb-1">{{ number_format($averageClassScore, 1) }}</h4>
                                <small class="text-muted">Rata-rata Nilai Kelas</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="bg-light rounded p-3">
                                @php
                                    $totalPossibleScores = $course->enrollments->count() * $course->meetings->count();
                                    $scoreCompletionPercentage = $totalPossibleScores > 0 ? ($scoredEntriesCount / $totalPossibleScores) * 100 : 0;
                                @endphp
                                <h4 class="fw-bold text-warning mb-1">{{ number_format($scoreCompletionPercentage, 0) }}%</h4>
                                <small class="text-muted">Progres Pengisian Nilai</small>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <small class="d-block text-muted mb-2">Nilai Kriteria:</small>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <div class="score-legend bg-primary text-white rounded-pill px-3 py-1">Creativity</div>
                                <div class="score-legend bg-info text-white rounded-pill px-3 py-1">Program</div>
                                <div class="score-legend bg-danger text-white rounded-pill px-3 py-1">Design</div>
                                <div class="score-legend bg-dark text-white rounded-pill px-3 py-1">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ---

    {{-- Charts Section --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold text-dark py-3">Perkembangan Rata-rata Nilai Kelas per Pertemuan</div>
                <div class="card-body">
                    <canvas id="classAverageChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-bold text-dark py-3">Rata-rata Nilai Total Siswa (Semua Pertemuan)</div>
                <div class="card-body">
                    <canvas id="studentAverageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    ---

    {{-- Scores Table --}}
    <div class="card mb-4 shadow-sm">
        <h5 class="card-header fw-bold text-dark py-3">Tabel Nilai Siswa per Pertemuan</h5>
        <div class="card-body p-0"> {{-- Hapus padding body jika tabel akan mengambil semua ruang --}}
            <div class="table-responsive text-nowrap"> {{-- Penting untuk tabel agar responsif --}}
                <table class="table table-hover table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-center align-middle bg-primary text-white" style="min-width: 50px;">No</th>
                            <th rowspan="2" class="text-start align-middle bg-primary text-white" style="min-width: 200px;">Nama Siswa</th>
                            <th rowspan="2" class="text-center align-middle bg-primary text-white" style="min-width: 100px;">Kelas</th>
                            @foreach($course->meetings as $meeting)
                            <th colspan="4" class="text-center bg-info text-white">
                                Pertemuan {{ $loop->iteration }}
                                <div class="small fw-normal mt-1">{{ $meeting->tanggal_pelaksanaan ? $meeting->tanggal_pelaksanaan->format('d/m/Y') : '-' }}</div>
                            </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($course->meetings as $meeting)
                            <th class="text-center bg-primary text-white">C</th> {{-- Singkat menjadi C, P, D, T --}}
                            <th class="text-center bg-info text-white">P</th>
                            <th class="text-center bg-danger text-white">D</th>
                            <th class="text-center bg-dark text-white">T</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($course->enrollments as $index => $enrollment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $enrollment->user->name }}</td>
                            <td class="text-center">
                                {{ $enrollment->kelas ? $enrollment->kelas->nama : '-' }}
                            </td>
                            @foreach($course->meetings as $meeting)
                                @php
                                    $score = App\Models\Score::where('peserta_id', $enrollment->user->id)
                                        ->where('meeting_id', $meeting->id)
                                        ->first();
                                    $creativity = ($score && $score->creativity_score !== null) ? number_format($score->creativity_score, 1) : '-';
                                    $program = ($score && $score->program_score !== null) ? number_format($score->program_score, 1) : '-';
                                    $design = ($score && $score->design_score !== null) ? number_format($score->design_score, 1) : '-';
                                    $total = ($score && $score->total_score !== null) ? number_format($score->total_score, 1) : '-';
                                @endphp
                                <td class="text-center {{ $creativity !== '-' && $creativity < 70 ? 'table-danger' : '' }}">{{ $creativity }}</td> {{-- Highlight nilai di bawah KKM (contoh 70) --}}
                                <td class="text-center {{ $program !== '-' && $program < 70 ? 'table-danger' : '' }}">{{ $program }}</td>
                                <td class="text-center {{ $design !== '-' && $design < 70 ? 'table-danger' : '' }}">{{ $design }}</td>
                                <td class="text-center fw-bold bg-light">{{ $total }}</td> {{-- Total score lebih menonjol --}}
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 3 + ($course->meetings->count() * 4) }}" class="text-center py-4">
                                <i class="bx bx-info-circle me-2"></i> Belum ada siswa terdaftar di kursus ini.
                            </td>
                        </tr>
                        @endforelse
                        {{-- Tambahkan baris rata-rata kelas per pertemuan --}}
                        @if($course->enrollments->count() > 0)
                        <tr class="table-info fw-bold">
                            <td colspan="3" class="text-end">Rata-rata Kelas per Pertemuan:</td>
                            @foreach($course->meetings as $meeting)
                                @php
                                    $meetingScores = App\Models\Score::where('meeting_id', $meeting->id)->get();
                                    $avgCreativity = $meetingScores->avg('creativity_score');
                                    $avgProgram = $meetingScores->avg('program_score');
                                    $avgDesign = $meetingScores->avg('design_score');
                                    $avgTotal = $meetingScores->avg('total_score');
                                @endphp
                                <td class="text-center">{{ $avgCreativity !== null ? number_format($avgCreativity, 1) : '-' }}</td>
                                <td class="text-center">{{ $avgProgram !== null ? number_format($avgProgram, 1) : '-' }}</td>
                                <td class="text-center">{{ $avgDesign !== null ? number_format($avgDesign, 1) : '-' }}</td>
                                <td class="text-center">{{ $avgTotal !== null ? number_format($avgTotal, 1) : '-' }}</td>
                            @endforeach
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    /* Umum */
.container-p-y {
    padding-top: 1.5rem !important;
    padding-bottom: 1.5rem !important;
}
.card {
    border-radius: 0.75rem;
    transition: all 0.2s ease-in-out;
}
.card-body {
    padding: 1.5rem;
}
.fw-bold {
    font-weight: 700 !important;
}
.fw-semibold {
    font-weight: 600 !important;
}
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
}

/* Header */
h4 .text-primary {
    color: #696cff !important; /* Warna primary Bootstrap */
}

/* Course Details & Summary Cards */
.card.border-start {
    border-left-width: 5px !important; /* Tebalkan border kiri */
}
.card-title {
    font-size: 1.25rem;
}
.badge.rounded-pill {
    font-size: 0.85em;
    font-weight: 600;
}
.score-legend {
    font-size: 0.85em;
    font-weight: 600;
}

/* Table Styles */
.table-responsive {
    border-radius: 0.75rem; /* Border radius untuk responsif tabel */
    overflow-x: auto; /* Pastikan scrollable horizontal */
}
.table {
    margin-bottom: 0; /* Hapus margin bawah default tabel */
}
.table thead th {
    vertical-align: middle;
    white-space: nowrap; /* Pastikan teks di header tidak pecah baris */
    padding: 0.75rem 0.5rem; /* Padding lebih kecil untuk header */
    font-size: 0.9em; /* Ukuran font lebih kecil di header */
}
.table tbody td {
    white-space: nowrap; /* Pastikan teks di sel tidak pecah baris */
    vertical-align: middle;
    padding: 0.6rem 0.5rem; /* Padding sel lebih kecil */
    font-size: 0.85em; /* Ukuran font lebih kecil di body */
}
.table-bordered th, .table-bordered td {
    border-color: #dee2e6 !important; /* Pastikan border tabel konsisten */
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.03); /* Warna stripe yang lebih halus */
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.075); /* Warna hover yang lebih jelas */
}
.table-danger {
    background-color: #fce8e8 !important; /* Latar belakang merah muda untuk nilai rendah */
    color: #d12f2f !important; /* Teks merah gelap */
    font-weight: 600;
}
.table-info {
    background-color: #e0f2f7 !important;
    color: #0d6efd !important;
}

/* Charts Specific Styles (tambahan) */
#classAverageChart, #studentAverageChart {
    max-height: 400px; /* Batasi tinggi grafik agar tidak terlalu besar */
    width: 100% !important; /* Pastikan lebar 100% */
}
.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1rem 1.5rem;
    font-size: 1.15rem;
}


/* Media Queries for Responsiveness */
@media (max-width: 767.98px) { /* Small devices (phones) */
    .d-flex.flex-wrap {
        justify-content: center !important; /* Pusatkan tombol di mobile */
    }
    .gap-2 > .btn {
        width: 100%; /* Tombol mengambil lebar penuh di mobile */
        margin-bottom: 0.5rem; /* Tambah jarak antar tombol */
    }
    .gap-2 > .btn:last-child {
        margin-bottom: 0;
    }

    .card-body {
        padding: 1rem; /* Kurangi padding card di mobile */
    }
    .card-title {
        font-size: 1.15rem; /* Ukuran judul card lebih kecil */
        text-align: center; /* Judul card di tengah */
    }
    .col-sm-6 {
        flex: 0 0 100%; /* Setiap detail mengambil 100% lebar */
        max-width: 100%;
    }
    .text-center.text-md-start {
        text-align: center !important; /* Pastikan teks di tengah di mobile */
    }

    .table thead th, .table tbody td {
        font-size: 0.75em; /* Ukuran font tabel lebih kecil di mobile */
        padding: 0.5rem 0.3rem; /* Padding lebih kecil */
    }
    /* Charts Specific Styles for Mobile */
    #classAverageChart, #studentAverageChart {
        height: 300px; /* Tinggi grafik di mobile */
    }
}

@media (min-width: 768px) and (max-width: 991.98px) { /* Medium devices (tablets) */
    .card-body {
        padding: 1.25rem;
    }
    .table thead th, .table tbody td {
        font-size: 0.8em;
        padding: 0.6rem 0.4rem;
    }
    /* Charts Specific Styles for Tablets */
    #classAverageChart, #studentAverageChart {
        height: 350px; /* Tinggi grafik di tablet */
    }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Controller
        const averageScoresPerMeeting = @json($averageScoresPerMeeting);
        const averageScoresPerStudent = @json($averageScoresPerStudent);

        // --- Grafik Rata-rata Kelas per Pertemuan (Line Chart) ---
        const classAvgCtx = document.getElementById('classAverageChart').getContext('2d');
        if (classAvgCtx) {
            new Chart(classAvgCtx, {
                type: 'line',
                data: {
                    labels: averageScoresPerMeeting.map(data => data.meeting_name + ' (' + data.date + ')'),
                    datasets: [
                        {
                            label: 'Creativity',
                            data: averageScoresPerMeeting.map(data => data.avg_creativity),
                            borderColor: '#696cff', // Primary color
                            backgroundColor: 'rgba(105, 108, 255, 0.2)',
                            tension: 0.4,
                            fill: false,
                        },
                        {
                            label: 'Program',
                            data: averageScoresPerMeeting.map(data => data.avg_program),
                            borderColor: '#0dcaf0', // Info color
                            backgroundColor: 'rgba(13, 202, 240, 0.2)',
                            tension: 0.4,
                            fill: false,
                        },
                        {
                            label: 'Design',
                            data: averageScoresPerMeeting.map(data => data.avg_design),
                            borderColor: '#dc3545', // Danger color
                            backgroundColor: 'rgba(220, 53, 69, 0.2)',
                            tension: 0.4,
                            fill: false,
                        },
                        {
                            label: 'Total',
                            data: averageScoresPerMeeting.map(data => data.avg_total),
                            borderColor: '#212529', // Dark color
                            backgroundColor: 'rgba(33, 37, 41, 0.2)',
                            tension: 0.4,
                            fill: false,
                            borderWidth: 2, // Lebih tebal
                            pointRadius: 4, // Titik lebih besar
                            pointBackgroundColor: '#212529',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100, // Nilai maksimal 100
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Pertemuan'
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: false, // Judul sudah ada di card header
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }


        // --- Grafik Rata-rata Nilai Total Siswa (Bar Chart) ---
        const studentAvgCtx = document.getElementById('studentAverageChart').getContext('2d');
        if (studentAvgCtx) {
            new Chart(studentAvgCtx, {
                type: 'bar',
                data: {
                    labels: averageScoresPerStudent.map(data => data.student_name),
                    datasets: [{
                        label: 'Rata-rata Nilai Total',
                        data: averageScoresPerStudent.map(data => data.avg_total_score),
                        backgroundColor: averageScoresPerStudent.map(data => data.avg_total_score < 70 ? '#dc3545' : '#198754'), // Hijau untuk lulus, merah untuk tidak lulus (contoh KKM 70)
                        borderColor: averageScoresPerStudent.map(data => data.avg_total_score < 70 ? '#dc3545' : '#198754'),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai Total'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Nama Siswa'
                            },
                            ticks: {
                                autoSkip: true, // Untuk menghindari label yang bertumpuk jika banyak siswa
                                maxRotation: 45, // Rotasi label
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: false,
                        },
                        legend: {
                            display: false // Tidak perlu legend jika hanya ada 1 dataset
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return context[0].label;
                                },
                                label: function(context) {
                                    return 'Rata-rata: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
