@extends('layouts.mentor.template')

@section('content')
{{-- Menggunakan background gradient biru yang elegan --}}
<div class="container-fluid py-4 min-height: 100vh;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center mb-4">
                        {{-- Menggunakan warna biru gelap untuk ikon header --}}
                        <div class="bg-blue-dark p-3 rounded-circle me-3 shadow-sm">
                            <i class="fas fa-chart-bar text-white fs-4"></i>
                        </div>
                        <div>
                            <h1 class="card-title mb-1 fw-bold text-dark">Rekap Nilai Kelas</h1>
                            <h4 class="text-blue-main mb-0">{{ $course->nama_kelas }}</h4> {{-- Warna aksen biru utama --}}
                        </div>
                    </div>
                    
                    {{-- Statistik Card - Kembali ke background putih dengan border biru --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="card bg-white text-dark border-blue-main h-100 shadow-sm rounded-3"> {{-- Kembali ke putih, border biru --}}
                                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <i class="fas fa-users fs-3 mb-2 text-blue-main"></i> {{-- Ikon biru --}}
                                    <h5 class="card-title fw-semibold text-dark">Total Siswa</h5>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $enrollments->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card bg-white text-dark border-blue-main h-100 shadow-sm rounded-3">
                                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <i class="fas fa-calendar-alt fs-3 mb-2 text-blue-main"></i>
                                    <h5 class="card-title fw-semibold text-dark">Total Pertemuan</h5>
                                    <h3 class="mb-0 fw-bold text-dark">{{ $meetings->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card bg-white text-dark border-blue-main h-100 shadow-sm rounded-3">
                                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <i class="fas fa-tasks fs-3 mb-2 text-blue-main"></i>
                                    <h5 class="card-title fw-semibold text-dark">Kategori Nilai</h5>
                                    <h3 class="mb-0 fw-bold text-dark">3</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card bg-white text-dark border-blue-main h-100 shadow-sm rounded-3">
                                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                                    <i class="fas fa-star fs-3 mb-2 text-blue-main"></i>
                                    <h5 class="card-title fw-semibold text-dark">Status</h5>
                                    <h3 class="mb-0 fw-bold text-dark">Aktif</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol-tombol Aksi --}}
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                        <a href="{{ route('mentor.score.exportExcel', $course->id) }}" class="btn btn-success btn-lg shadow-sm rounded-pill px-4">
                            <i class="fas fa-file-excel me-2"></i>Export Excel
                        </a>
                        <a href="{{ route('mentor.score.exportPdf', $course->id) }}" class="btn btn-danger btn-lg shadow-sm rounded-pill px-4">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </a>
                        <a href="{{ route('mentor.score.index') }}" class="btn btn-secondary btn-lg shadow-sm rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button class="btn bg-blue-main text-white btn-lg shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter me-2"></i>Filter Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($enrollments->isNotEmpty())
        {{-- Card Rata-rata Nilai per Pertemuan (Grafik) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-gradient-blue text-white p-3 rounded-top-4"> {{-- Header biru --}}
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="fas fa-chart-line me-2"></i>Rata-rata Nilai per Pertemuan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="averageScoreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Tabel Rekap Nilai --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-gradient-blue text-white d-flex justify-content-between align-items-center p-3 rounded-top-4"> {{-- Header biru --}}
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="fas fa-table me-2"></i>Tabel Rekap Nilai
                        </h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-light btn-sm px-3" onclick="toggleView('table')">
                                <i class="fas fa-table"></i> Table
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm px-3" onclick="toggleView('cards')">
                                <i class="fas fa-th-large"></i> Cards
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="tableView" class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th rowspan="2" class="align-middle text-center border-end text-dark" style="min-width: 200px; background-color: #f8f9fa;">
                                            <i class="fas fa-user me-2"></i>Nama Peserta
                                        </th>
                                        @foreach($meetings as $meeting)
                                            <th colspan="4" class="text-center border-end bg-light text-dark"> {{-- Background light --}}
                                                <i class="fas fa-calendar-day me-2"></i>Pertemuan {{ $meeting->pertemuan }}
                                            </th>
                                        @endforeach
                                        <th rowspan="2" class="text-center align-middle bg-success text-white">
                                            <i class="fas fa-calculator me-2"></i>Rata-rata<br>Keseluruhan
                                        </th>
                                    </tr>
                                    <tr>
                                        @foreach($meetings as $meeting)
                                            <th class="text-center bg-light text-dark"> {{-- Background light --}}
                                                <i class="fas fa-lightbulb me-1"></i>Creativity
                                            </th>
                                            <th class="text-center bg-light text-dark"> {{-- Background light --}}
                                                <i class="fas fa-palette me-1"></i>Design
                                            </th>
                                            <th class="text-center bg-light text-dark"> {{-- Background light --}}
                                                <i class="fas fa-code me-1"></i>Programming
                                            </th>
                                            <th class="text-center border-end bg-secondary text-white"> {{-- Background secondary --}}
                                                <i class="fas fa-chart-line me-1"></i>Rata-rata
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $index => $student)
                                        <tr class="student-row" data-student-id="{{ $student->id }}">
                                            <td class="fw-bold border-end bg-white">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-blue-main text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $student->name }}</div>
                                                        <small class="text-muted">ID: {{ $student->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            @php
                                                $studentOverallTotalScore = 0;
                                                $studentOverallMeetingCount = 0;
                                            @endphp

                                            @foreach($meetings as $meeting)
                                                @php
                                                    $score = $scoresData[$student->id . '-' . $meeting->id][0] ?? null;
                                                    
                                                    $creativity = $score->creativity_score ?? null;
                                                    $design = $score->design_score ?? null;
                                                    $program = $score->program_score ?? null;
                                                    
                                                    $meetingScores = array_filter([$creativity, $design, $program], function($val) {
                                                        return !is_null($val);
                                                    });

                                                    $avgMeeting = null;
                                                    if (!empty($meetingScores)) {
                                                        $avgMeeting = array_sum($meetingScores) / count($meetingScores);
                                                        $studentOverallTotalScore += $avgMeeting;
                                                        $studentOverallMeetingCount++;
                                                    }
                                                @endphp
                                                <td class="text-center">
                                                    @if(is_null($creativity))
                                                        <span class="badge bg-secondary-subtle text-secondary py-2 px-3">-</span>
                                                    @else
                                                        {{-- Badge warna netral atau disesuaikan dengan nilai --}}
                                                        <span class="badge bg-light text-dark py-2 px-3">{{ number_format($creativity, 1) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(is_null($design))
                                                        <span class="badge bg-secondary-subtle text-secondary py-2 px-3">-</span>
                                                    @else
                                                        <span class="badge bg-light text-dark py-2 px-3">{{ number_format($design, 1) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if(is_null($program))
                                                        <span class="badge bg-secondary-subtle text-secondary py-2 px-3">-</span>
                                                    @else
                                                        <span class="badge bg-light text-dark py-2 px-3">{{ number_format($program, 1) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center border-end">
                                                    @if(is_null($avgMeeting))
                                                        <span class="badge bg-light text-muted fs-6 py-2 px-3">-</span>
                                                    @else
                                                        @php
                                                            $badgeClass = 'bg-success'; // Tetap gunakan success/danger/warning
                                                            if ($avgMeeting < 60) $badgeClass = 'bg-danger';
                                                            elseif ($avgMeeting < 75) $badgeClass = 'bg-warning';
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} fs-6 py-2 px-3">{{ number_format($avgMeeting, 1) }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center bg-white">
                                                @if($studentOverallMeetingCount > 0)
                                                    @php
                                                        $overallAvg = $studentOverallTotalScore / $studentOverallMeetingCount;
                                                        $badgeClass = 'bg-success';
                                                        if ($overallAvg < 60) $badgeClass = 'bg-danger';
                                                        elseif ($overallAvg < 75) $badgeClass = 'bg-warning';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} fs-5 py-2 px-3">{{ number_format($overallAvg, 1) }}</span>
                                                @else
                                                    <span class="badge bg-light text-muted fs-5 py-2 px-3">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div id="cardsView" class="p-4" style="display: none;">
                            <div class="row g-4">
                                @foreach($enrollments as $student)
                                    <div class="col-lg-6 col-xl-4">
                                        <div class="card h-100 shadow-sm border-0 rounded-4 student-card">
                                            <div class="card-header bg-gradient-blue text-white p-3 rounded-top-4"> {{-- Header biru --}}
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-blue-main text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1.2rem;">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-white">{{ $student->name }}</h6>
                                                        <small class="opacity-75">ID: {{ $student->id }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $studentOverallTotalScore = 0;
                                                    $studentOverallMeetingCount = 0;
                                                @endphp
                                                
                                                @foreach($meetings as $meeting)
                                                    @php
                                                        $score = $scoresData[$student->id . '-' . $meeting->id][0] ?? null;
                                                        
                                                        $creativity = $score->creativity_score ?? null;
                                                        $design = $score->design_score ?? null;
                                                        $program = $score->program_score ?? null;
                                                        
                                                        $meetingScores = array_filter([$creativity, $design, $program], function($val) {
                                                            return !is_null($val);
                                                        });

                                                        $avgMeeting = null;
                                                        if (!empty($meetingScores)) {
                                                            $avgMeeting = array_sum($meetingScores) / count($meetingScores);
                                                            $studentOverallTotalScore += $avgMeeting;
                                                            $studentOverallMeetingCount++;
                                                        }
                                                    @endphp
                                                    
                                                    <div class="mb-3 p-3 bg-light rounded-3 border">
                                                        <h6 class="text-dark mb-3 fw-bold"> {{-- Text dark --}}
                                                            <i class="fas fa-calendar-day me-2 text-blue-main"></i>Pertemuan {{ $meeting->pertemuan }} {{-- Ikon biru --}}
                                                        </h6>
                                                        <div class="row g-2 mb-2">
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <small class="text-muted d-block mb-1">Creativity</small>
                                                                    <span class="badge bg-light text-dark">{{ is_null($creativity) ? '-' : number_format($creativity, 1) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <small class="text-muted d-block mb-1">Design</small>
                                                                    <span class="badge bg-light text-dark">{{ is_null($design) ? '-' : number_format($design, 1) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-center">
                                                                    <small class="text-muted d-block mb-1">Programming</small>
                                                                    <span class="badge bg-light text-dark">{{ is_null($program) ? '-' : number_format($program, 1) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-center mt-3 pt-2 border-top">
                                                            <strong class="text-muted">Rata-rata: </strong>
                                                            @if(is_null($avgMeeting))
                                                                <span class="badge bg-secondary-subtle text-secondary">-</span>
                                                            @else
                                                                @php
                                                                    $badgeClass = 'bg-success';
                                                                    if ($avgMeeting < 60) $badgeClass = 'bg-danger';
                                                                    elseif ($avgMeeting < 75) $badgeClass = 'bg-warning';
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ number_format($avgMeeting, 1) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="card-footer bg-light text-center p-3 rounded-bottom-4">
                                                <strong class="text-dark">Rata-rata Keseluruhan: </strong>
                                                @if($studentOverallMeetingCount > 0)
                                                    @php
                                                        $overallAvg = $studentOverallTotalScore / $studentOverallMeetingCount;
                                                        $badgeClass = 'bg-success';
                                                        if ($overallAvg < 60) $badgeClass = 'bg-danger';
                                                        elseif ($overallAvg < 75) $badgeClass = 'bg-warning';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} fs-6 py-1 px-2">{{ number_format($overallAvg, 1) }}</span>
                                                @else
                                                    <span class="badge bg-secondary fs-6 py-1 px-2">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4 text-center" style="background: rgba(255, 255, 255, 0.95);">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <i class="fas fa-users-slash text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">Belum Ada Data</h3>
                        <p class="text-muted mb-4">Belum ada siswa atau data nilai di kelas ini.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('mentor.score.index') }}" class="btn bg-blue-main text-white rounded-pill px-4 py-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Kelas
                            </a>
                            <button class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                <i class="fas fa-refresh me-2"></i>Refresh Halaman
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-blue-main text-white border-0 rounded-top-4"> {{-- Header modal biru --}}
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="fas fa-filter me-2"></i>Filter Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="filterForm">
                    <input type="hidden" name="course_id" value="{{ $course->id }}"> {{-- Penting untuk menjaga ID kelas --}}

                    <div class="mb-3">
                        <label for="studentNameFilter" class="form-label fw-semibold">Filter berdasarkan Nama Siswa:</label>
                        <input type="text" class="form-control rounded-pill" id="studentNameFilter" name="student_name" value="{{ request('student_name') }}" placeholder="Cari nama siswa...">
                    </div>

                    <div class="mb-3">
                        <label for="meetingFilter" class="form-label fw-semibold">Filter berdasarkan Pertemuan:</label>
                        <select class="form-select rounded-pill" id="meetingFilter" name="meeting_id">
                            <option value="">Semua Pertemuan</option>
                            @foreach($meetings as $meeting)
                                <option value="{{ $meeting->id }}" {{ request('meeting_id') == $meeting->id ? 'selected' : '' }}>Pertemuan {{ $meeting->pertemuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gradeFilter" class="form-label fw-semibold">Filter berdasarkan Rata-rata Keseluruhan Nilai:</label>
                        <select class="form-select rounded-pill" id="gradeFilter" name="grade_filter">
                            <option value="">Semua Nilai</option>
                            <option value="high" {{ request('grade_filter') == 'high' ? 'selected' : '' }}>Nilai Tinggi (≥75)</option>
                            <option value="medium" {{ request('grade_filter') == 'medium' ? 'selected' : '' }}>Nilai Sedang (60-74)</option>
                            <option value="low" {{ request('grade_filter') == 'low' ? 'selected' : '' }}>Nilai Rendah (&lt;60)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn bg-blue-main text-white rounded-pill px-4" onclick="applyFilter()">Terapkan Filter</button>
            </div>
        </div>
    </div>
</div>

{{-- Memuat Chart.js dari CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data grafik dari backend Laravel
const chartLabels = @json($chartLabels);
const chartDataCreativity = @json($chartDataCreativity);
const chartDataDesign = @json($chartDataDesign);
const chartDataProgramming = @json($chartDataProgramming);
const chartDataAverage = @json($chartDataAverage);

// Render Chart
let averageScoreChart; // Deklarasikan di luar agar bisa diakses dan dihancurkan
function renderChart() {
    const ctx = document.getElementById('averageScoreChart').getContext('2d');

    // Hancurkan chart sebelumnya jika ada
    if (averageScoreChart) {
        averageScoreChart.destroy();
    }

    averageScoreChart = new Chart(ctx, {
        type: 'line', // Jenis grafik: garis
        data: {
            labels: chartLabels, // Label sumbu X (Pertemuan 1, Pertemuan 2, dst.)
            datasets: [
                {
                    label: 'Rata-rata Kreativitas',
                    data: chartDataCreativity,
                    borderColor: 'rgba(100, 181, 246, 0.8)', // Biru lebih cerah
                    backgroundColor: 'rgba(100, 181, 246, 0.2)',
                    fill: false,
                    tension: 0.3 // Garis sedikit melengkung
                },
                {
                    label: 'Rata-rata Desain',
                    data: chartDataDesign,
                    borderColor: 'rgba(255, 193, 7, 0.8)', // Kuning (warning)
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Rata-rata Programming',
                    data: chartDataProgramming,
                    borderColor: 'rgba(220, 53, 69, 0.8)', // Merah (danger)
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Rata-rata Keseluruhan',
                    data: chartDataAverage,
                    borderColor: 'rgba(40, 167, 69, 1)', // Hijau (success)
                    backgroundColor: 'rgba(40, 167, 69, 0.4)',
                    fill: true, // Isi area di bawah garis
                    tension: 0.3,
                    borderWidth: 2,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(40, 167, 69, 1)',
                    pointRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Penting untuk kontrol ukuran
            plugins: {
                title: {
                    display: true,
                    text: 'Tren Rata-rata Nilai per Pertemuan',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    color: '#333'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' %';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Nilai Rata-rata (%)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Pertemuan'
                    }
                }
            }
        }
    });
}


// Fungsi untuk toggle tampilan tabel/card
function toggleView(viewType) {
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    
    if (viewType === 'table') {
        tableView.style.display = 'block';
        cardsView.style.display = 'none';
    } else {
        tableView.style.display = 'none';
        cardsView.style.display = 'block';
    }
}

// Fungsi untuk menerapkan filter
function applyFilter() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    // Tambahkan parameter filter yang tidak kosong ke URL
    for (const [key, value] of formData.entries()) {
        if (value !== '') {
            params.append(key, value);
        }
    }

    // Untuk filter nilai rata-rata keseluruhan (high/medium/low), kita akan lakukan di frontend
    // karena kompleksitas query di backend.
    const gradeFilterValue = document.getElementById('gradeFilter').value;
    if (gradeFilterValue) {
        params.append('grade_filter', gradeFilterValue);
    }
    
    // Redirect ke URL dengan parameter filter
    window.location.href = "{{ route('mentor.score.showReport', $course->id) }}?" + params.toString();
    
    // Sembunyikan modal setelah menerapkan filter
    const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
    modal.hide();
}


// Efek animasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Render chart saat DOM selesai dimuat
    renderChart();

    // Animate cards on load
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 80);
    });

    // Student row hover effect for table
    document.querySelectorAll('.student-row').forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.style.backgroundColor = 'var(--blue-lighter)'; // Tetap biru muda saat hover
        });
        row.addEventListener('mouseleave', () => {
            row.style.backgroundColor = '';
        });
    });

    // Handle filter for overall average score in frontend (because backend query is complex)
    // This part is for *displaying* based on the `grade_filter` query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const gradeFilter = urlParams.get('grade_filter');

    if (gradeFilter) {
        document.querySelectorAll('.student-row').forEach(row => {
            const overallAvgBadge = row.querySelector('.text-center.bg-white .badge');
            if (overallAvgBadge && overallAvgBadge.textContent !== '-') {
                const overallAvg = parseFloat(overallAvgBadge.textContent);
                let showRow = false;

                if (gradeFilter === 'high' && overallAvg >= 75) {
                    showRow = true;
                } else if (gradeFilter === 'medium' && overallAvg >= 60 && overallAvg < 75) {
                    showRow = true;
                } else if (gradeFilter === 'low' && overallAvg < 60) {
                    showRow = true;
                }

                row.style.display = showRow ? '' : 'none';
            } else if (gradeFilter !== '') {
                // If there's a filter and no average score, hide the row
                row.style.display = 'none';
            }
        });
        
        // Also filter cards view
        document.querySelectorAll('.student-card').forEach(card => {
            const overallAvgBadge = card.querySelector('.card-footer .badge');
            if (overallAvgBadge && overallAvgBadge.textContent !== '-') {
                const overallAvg = parseFloat(overallAvgBadge.textContent);
                let showCard = false;

                if (gradeFilter === 'high' && overallAvg >= 75) {
                    showCard = true;
                } else if (gradeFilter === 'medium' && overallAvg >= 60 && overallAvg < 75) {
                    showCard = true;
                } else if (gradeFilter === 'low' && overallAvg < 60) {
                    showCard = true;
                }

                card.parentElement.style.display = showCard ? 'block' : 'none';
            } else if (gradeFilter !== '') {
                card.parentElement.style.display = 'none';
            }
        });
    }
});
</script>

<style>
/* Custom Color Palette (Biru) */
:root {
    --blue-dark: #1976d2;     /* Darker Blue (e.g., Material Blue 700) */
    --blue-main: #2196f3;     /* Main Blue (e.g., Material Blue 500) */
    --blue-accent: #64b5f6;   /* Lighter Blue (e.g., Material Blue 300) */
    --blue-light: #bbdefb;    /* Very Light Blue (e.g., Material Blue 100) */
    --blue-lighter: #e3f2fd;  /* Even Lighter Blue (e.g., Material Blue 50) */
    --blue-subtle: #90caf9;   /* Muted Blue (e.g., Material Blue 200) */
}

/* Hanya gunakan warna ini untuk aksen dan teks */
.bg-blue-dark { background-color: var(--blue-dark) !important; }
.bg-blue-main { background-color: var(--blue-main) !important; }
.bg-blue-accent { background-color: var(--blue-accent) !important; }
/* Tidak perlu bg-blue-light, bg-blue-lighter, bg-blue-subtle jika hanya header yang berwarna */

.text-blue-dark { color: var(--blue-dark) !important; }
.text-blue-main { color: var(--blue-main) !important; }
.text-blue-accent { color: var(--blue-accent) !important; }
.text-blue-light { color: var(--blue-light) !important; }
.text-blue-lighter { color: var(--blue-lighter) !important; }
.text-blue-subtle { color: var(--blue-subtle) !important; }

/* Custom Gradient untuk card header dan lainnya */
.bg-gradient-blue {
    background: linear-gradient(45deg, var(--blue-dark) 0%, var(--blue-main) 100%) !important;
}

/* Border biru untuk card statistik */
.border-blue-main {
    border: 1px solid var(--blue-main) !important;
}

/* General Styling */
body {
    overflow-x: hidden;
}

.rounded-4 {
    border-radius: 1.25rem !important;
}

.card {
    transition: transform 0.4s ease-out, box-shadow 0.4s ease-out;
    border: none;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
}

.card-title {
    font-size: 1.4rem;
}

/* Badge Styling - Default ke netral, hanya rata-rata utama yang berwarna */
.badge {
    font-weight: 700;
    padding: 0.5em 1em;
    border-radius: 0.5rem;
    min-width: 40px;
    display: inline-block;
}

/* Warna badge spesifik untuk nilai komponen - Kembali ke light/dark */
.badge.bg-light.text-dark {
    background-color: #f8f9fa !important; /* Bootstrap light */
    color: #212529 !important; /* Bootstrap dark */
}

/* Table Specific Styling */
.table th {
    font-weight: 700;
    font-size: 0.95rem;
}

.table thead th {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.75rem;
}

.student-row:hover {
    background-color: var(--blue-lighter) !important; /* Light blue on hover */
    transition: background-color 0.3s ease;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1020;
}

/* Chart.js container styling */
#averageScoreChart {
    max-height: 400px; /* Batasi tinggi grafik */
    width: 100%; /* Pastikan grafik mengisi lebar */
}

/* Media Queries untuk Responsivitas */
@media (max-width: 767.98px) {
    .p-md-5 {
        padding: 1.5rem !important;
    }
    .fs-4 {
        font-size: 1.25rem !important;
    }
    .fs-3 {
        font-size: 1.5rem !important;
    }
    .btn-lg {
        font-size: 0.9rem;
        padding: 0.6rem 1.2rem;
    }
    .table-responsive {
        font-size: 0.8rem;
    }
    .table th, .table td {
        padding: 0.5rem;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.3em 0.6em;
    }
}
</style>
@endsection