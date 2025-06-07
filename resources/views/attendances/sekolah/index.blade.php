@extends('layouts.sekolah.template')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Laporan Absensi Siswa</h2>
                    <p class="text-muted mb-0">Kelola dan pantau kehadiran siswa secara real-time</p>
                </div>
                <div class="text-end">
                    <small class="text-muted">Total Data: <span class="fw-bold">{{ $attendances->total() }}</span></small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="card-title mb-0 text-white">
                <i class="bi bi-funnel me-2 text-white"></i>Filter Absensi
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('attendances.sekolah.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="tanggal" class="form-label fw-semibold">
                            <i class="bi bi-calendar3 me-1"></i>Tanggal
                        </label>
                        <input type="date" name="tanggal" id="tanggal" 
                               class="form-control form-control-lg" 
                               value="{{ request('tanggal') }}">
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="tipe" class="form-label fw-semibold">
                            <i class="bi bi-bookmark me-1"></i>Jenis Absensi
                        </label>
                        <select name="tipe" id="tipe" class="form-select form-select-lg">
                            <option value="">Semua Jenis</option>
                            <option value="kursus" {{ request('tipe') == 'kursus' ? 'selected' : '' }}>
                                📚 Kursus
                            </option>
                            <option value="kegiatan" {{ request('tipe') == 'kegiatan' ? 'selected' : '' }}>
                                🎯 Kegiatan
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="course_id" class="form-label fw-semibold">
                            <i class="bi bi-book me-1"></i>Kursus
                        </label>
                        <select name="course_id" id="course_id" class="form-select form-select-lg">
                            <option value="">Semua Kursus</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label for="status" class="form-label fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>Status Kehadiran
                        </label>
                        <select name="status" id="status" class="form-select form-select-lg">
                            <option value="">Semua Status</option>
                            <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>
                                ✅ Hadir
                            </option>
                            <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>
                                📝 Izin
                            </option>
                            <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>
                                🏥 Sakit
                            </option>
                            <option value="Tidak Hadir" {{ request('status') == 'Tidak Hadir' ? 'selected' : '' }}>
                                ❌ Tidak Hadir
                            </option>
                        </select>
                    </div>
                </div>               
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('attendances.sekolah.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-1"></i>Terapkan Filter
                            </button>
                            <button type="submit" name="export_excel" value="1" class="btn btn-success btn-lg px-4">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $attendances->where('status', 'Hadir')->count() }}</h3>
                    <p class="text-muted mb-0 small">Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $attendances->where('status', 'Izin')->count() }}</h3>
                    <p class="text-muted mb-0 small">Izin</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-heart-pulse-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="fw-bold text-info mb-1">{{ $attendances->where('status', 'Sakit')->count() }}</h3>
                    <p class="text-muted mb-0 small">Sakit</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="bi bi-x-circle-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="fw-bold text-danger mb-1">{{ $attendances->where('status', 'Tidak Hadir')->count() }}</h3>
                    <p class="text-muted mb-0 small">Tidak Hadir</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i>Data Absensi
                </h5>
                {{-- <div class="d-flex gap-2">
                    <button class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download me-1"></i>Export Excel
                    </button>
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-pdf me-1"></i>Export PDF
                    </button>
                </div> --}}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">#</th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">
                                <i class="bi bi-person me-1"></i>Nama Siswa
                            </th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">
                                <i class="bi bi-shield me-1"></i>Role
                            </th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">
                                <i class="bi bi-bookmark me-1"></i>Kursus/Kegiatan
                            </th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark text-center">
                                <i class="bi bi-check-circle me-1"></i>Status
                            </th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark">
                                <i class="bi bi-calendar me-1"></i>Tanggal
                            </th>
                            <th class="border-0 px-4 py-3 fw-bold text-dark text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="fw-bold text-muted">
                                        {{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <span class="text-white fw-bold">
                                                {{ substr($attendance->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $attendance->user->name }}</div>
                                            <small class="text-muted">{{ $attendance->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark border">
                                        {{ $attendance->user->role->name ?? 'Tidak Diketahui' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">
                                        @if($attendance->course_id && $attendance->course)
                                            <i class="bi bi-book text-primary me-1"></i>
                                            {{ $attendance->course->nama_kelas }}
                                        @elseif($attendance->activity_id && $attendance->activity)
                                            <i class="bi bi-calendar-event text-success me-1"></i>
                                            {{ $attendance->activity->nama_kegiatan }}
                                        @else
                                            <em class="text-muted">Tidak diketahui</em>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusConfig = [
                                            'Hadir' => ['class' => 'bg-success', 'icon' => 'check-circle-fill'],
                                            'Izin' => ['class' => 'bg-warning', 'icon' => 'exclamation-circle-fill'],
                                            'Sakit' => ['class' => 'bg-info', 'icon' => 'heart-pulse-fill'],
                                            'Tidak Hadir' => ['class' => 'bg-danger', 'icon' => 'x-circle-fill']
                                        ];
                                        $config = $statusConfig[$attendance->status] ?? ['class' => 'bg-secondary', 'icon' => 'question-circle-fill'];
                                    @endphp
                                    <span class="badge {{ $config['class'] }} px-3 py-2">
                                        <i class="bi bi-{{ $config['icon'] }} me-1"></i>
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->tanggal)->format('l') }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('attendances.sekolah.show', $attendance->id) }}" 
                                       class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <div class="mt-3">
                                            <h5 class="fw-bold">Belum Ada Data</h5>
                                            <p class="mb-0">Data absensi tidak ditemukan. Silakan coba filter lain.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($attendances->hasPages())
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan {{ $attendances->firstItem() }} - {{ $attendances->lastItem() }} 
                    dari {{ $attendances->total() }} data
                </div>
                <div>
                    {{ $attendances->onEachSide(1)->links('pagination.custom') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.avatar-initial {
    font-size: 1.1rem;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>
@endsection