@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            {{-- Icon: Chart Bar (fas fa-chart-bar) -> bi-bar-chart-fill --}}
                            <i class="bi bi-backpack2 fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h2 class="h3 mb-1 text-white">Rekap Absensi Kelas</h2>
                            <p class="mb-0 text-white-50">{{ $course->nama_kelas }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-shape bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{-- Icon: Check (fas fa-check) -> bi-check-circle-fill --}}
                        <i class="bi bi-clock-fill text-white fa-2x"></i>
                    </div>
                    <h5 class="text-success mb-1">Total Pertemuan</h5>
                    <h3 class="mb-0">{{ count($meetings) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-shape bg-info bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{-- Icon: Users (fas fa-users) -> bi-people-fill --}}
                        <i class="bi bi-people text-white fa-2x"></i>
                    </div>
                    <h5 class="text-info mb-1">Total Siswa</h5>
                    <h3 class="mb-0">{{ count($students) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-shape bg-warning bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{-- Icon: Percentage (fas fa-percentage) -> bi-percent --}}
                        <i class="bi bi-percent text-white fa-2x"></i>
                    </div>
                    <h5 class="text-warning mb-1">Rata-rata Kehadiran</h5>
                    <h3 class="mb-0">
                        @php
                            $totalHadir = 0;
                            $totalPertemuan = count($meetings) * count($students);
                            foreach($students as $student) {
                                $totalHadir += $student->attendances->where('status', 'Hadir')->count();
                            }
                            $persentase = $totalPertemuan > 0 ? round(($totalHadir / $totalPertemuan) * 100, 1) : 0;
                        @endphp
                        {{ $persentase }}%
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-shape bg-primary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        {{-- Icon: Calendar Alt (fas fa-calendar-alt) -> bi-calendar-check-fill --}}
                        <i class="bi bi-calendar-check-fill text-white fa-2x"></i>
                    </div>
                    <h5 class="text-primary mb-1">Periode</h5>
                    <p class="mb-0 small">{{ $course->waktu_mulai }} - {{ $course->waktu_akhir }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">Detail Absensi Per Siswa</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="exportToExcel()">
                                {{-- Icon: Download (fas fa-download) -> bi-download --}}
                                <i class="bi bi-download me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="attendanceTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 fw-semibold text-dark" rowspan="2" style="vertical-align: middle; min-width: 200px;">
                                        {{-- Icon: User (fas fa-user) -> bi-person-fill --}}
                                        <i class="bi bi-person-fill me-2 text-muted"></i>Nama Siswa
                                    </th>
                                    @foreach ($meetings as $meeting)
                                        <th class="px-3 py-3 text-center fw-semibold text-dark" style="min-width: 100px;">
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="badge bg-primary bg-opacity-10 text-white mb-1">P{{ $meeting->pertemuan }}</span>
                                                <small class="text-muted">{{ date('d/m', strtotime($meeting->tanggal_pelaksanaan ?? now())) }}</small>
                                            </div>
                                        </th>
                                    @endforeach
                                    <th class="px-3 py-3 text-center fw-semibold text-success" rowspan="2" style="vertical-align: middle;">
                                        {{-- Icon: Check Circle (fas fa-check-circle) -> bi-check-circle-fill --}}
                                        <i class="bi bi-check-circle-fill me-1"></i>Hadir
                                    </th>
                                    <th class="px-3 py-3 text-center fw-semibold text-danger" rowspan="2" style="vertical-align: middle;">
                                        {{-- Icon: Times Circle (fas fa-times-circle) -> bi-x-circle-fill --}}
                                        <i class="bi bi-x-circle-fill me-1"></i>Tidak Hadir
                                    </th>
                                    <th class="px-3 py-3 text-center fw-semibold text-warning" rowspan="2" style="vertical-align: middle;">
                                        {{-- Icon: Exclamation Circle (fas fa-exclamation-circle) -> bi-exclamation-circle-fill --}}
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>Izin
                                    </th>
                                    <th class="px-3 py-3 text-center fw-semibold text-info" rowspan="2" style="vertical-align: middle;">
                                        {{-- Icon: Thermometer Half (fas fa-thermometer-half) -> bi-bandaid-fill (or bi-thermometer-half if available) --}}
                                        {{-- Note: bi-thermometer-half is not directly available, bi-bandaid-fill is a good alternative for 'Sakit' --}}
                                        <i class="bi bi-bandaid-fill me-1"></i>Sakit
                                    </th>
                                </tr>
                                <tr class="bg-light">
                                    @foreach ($meetings as $meeting)
                                        <th class="px-3 py-2 text-center text-muted small">Status</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr class="border-bottom {{ $index % 2 == 0 ? 'bg-white' : 'bg-light bg-opacity-25' }}">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-bold">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-dark">{{ $student->name }}</h6>
                                                    <small class="text-muted">{{ $student->email ?? 'No email' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach ($meetings as $meeting)
                                            @php
                                                $attendance = $student->attendances->where('meeting_id', $meeting->id)->first();
                                                $status = $attendance ? $attendance->status : '-';
                                            @endphp
                                            <td class="px-3 py-3 text-center">
                                                @if($status == 'Hadir')
                                                    <span class="badge bg-success bg-opacity-15 text-white border border-success border-opacity-25">
                                                        {{-- Icon: Check (fas fa-check) -> bi-check --}}
                                                        <i class="bi bi-check me-1"></i>{{ $status }}
                                                    </span>
                                                @elseif($status == 'Tidak Hadir')
                                                    <span class="badge bg-danger bg-opacity-15 text-white border border-danger border-opacity-25">
                                                        {{-- Icon: Times (fas fa-times) -> bi-x --}}
                                                        <i class="bi bi-x me-1"></i>Alpha
                                                    </span>
                                                @elseif($status == 'Izin')
                                                    <span class="badge bg-warning bg-opacity-15 text-white border border-warning border-opacity-25">
                                                        {{-- Icon: Exclamation (fas fa-exclamation) -> bi-exclamation --}}
                                                        <i class="bi bi-exclamation me-1"></i>{{ $status }}
                                                    </span>
                                                @elseif($status == 'Sakit')
                                                    <span class="badge bg-info bg-opacity-15 text-white border border-info border-opacity-25">
                                                        {{-- Icon: Thermometer Half (fas fa-thermometer-half) -> bi-bandaid (or bi-thermometer if available) --}}
                                                        <i class="bi bi-bandaid me-1"></i>{{ $status }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary bg-opacity-15 text-white border border-secondary border-opacity-25">
                                                        {{-- Icon: Minus (fas fa-minus) -> bi-dash --}}
                                                        <i class="bi bi-dash me-1"></i>{{ $status }}
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                        @php
                                            $hadir = $student->attendances->where('status', 'Hadir')->count();
                                            $tidakHadir = $student->attendances->where('status', 'Tidak Hadir')->count();
                                            $izin = $student->attendances->where('status', 'Izin')->count();
                                            $sakit = $student->attendances->where('status', 'Sakit')->count();
                                        @endphp
                                        <td class="px-3 py-3 text-center">
                                            <span class="badge bg-success fs-6 px-3 py-2">{{ $hadir }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="badge bg-danger fs-6 px-3 py-2">{{ $tidakHadir }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="badge bg-warning fs-6 px-3 py-2">{{ $izin }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="badge bg-info fs-6 px-3 py-2">{{ $sakit }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS untuk tampilan modern */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Contoh gradien ungu-biru */
}

.icon-shape {
    transition: all 0.3s ease;
}

.card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.table th {
    border: none;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.table td {
    border: none;
    vertical-align: middle;
}

/* Penyesuaian untuk badge status per pertemuan */
.badge.bg-success.bg-opacity-15 { background-color: rgba(40, 167, 69, 0.15) !important; color: #28a745 !important; }
.badge.bg-danger.bg-opacity-15 { background-color: rgba(220, 53, 69, 0.15) !important; color: #dc3545 !important; }
.badge.bg-warning.bg-opacity-15 { background-color: rgba(255, 193, 7, 0.15) !important; color: #ffc107 !important; }
.badge.bg-info.bg-opacity-15 { background-color: rgba(23, 162, 184, 0.15) !important; color: #17a2b8 !important; }
.badge.bg-secondary.bg-opacity-15 { background-color: rgba(108, 117, 125, 0.15) !important; color: #6c757d !important; }

.badge {
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
}

.avatar {
    font-size: 0.875rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.table-responsive {
    border-radius: 12px;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 0.75rem;
    }
}
</style>

<script>
function exportToExcel() {
    // Implementasi export to Excel
    alert('Fitur export Excel akan segera tersedia');
}

function printTable() {
    window.print();
}

// Auto refresh setiap 5 menit
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endsection