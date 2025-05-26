@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient-blue text-white p-4 rounded-top-4">
            <h2 class="card-title mb-0 fw-bold fs-4 text-white">
                <i class="fas fa-clipboard-check me-2"></i> Rekap Absensi Siswa
            </h2>
        </div>
        <div class="card-body p-4">
            @forelse ($courses as $course)
                <div class="card mb-3 shadow-sm border-0 rounded-3 class-item">
                    <a href="{{ route('mentor.attendance.show', $course->id) }}" class="text-decoration-none">
                        <div class="card-body d-flex align-items-center py-3 px-4">
                            <div class="icon-circle bg-blue-main text-white me-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-chalkboard fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">{{ $course->nama_kelas }}</h5>
                                <small class="text-muted">Kode: {{ $course->kode_unik }} | Level: {{ $course->level }}</small>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-blue-main"></i>
                        </div>
                    </a>
                </div>
            @empty
                <div class="alert alert-info text-center py-4 rounded-3">
                    <i class="fas fa-info-circle me-2"></i> Belum ada kelas yang Anda ajar.
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
/* Custom Color Palette (Biru) - Pastikan ini konsisten dengan file lain */
:root {
    --blue-dark: #1976d2;     /* Darker Blue (e.g., Material Blue 700) */
    --blue-main: #2196f3;     /* Main Blue (e.g., Material Blue 500) */
    --blue-accent: #64b5f6;   /* Lighter Blue (e.g., Material Blue 300) */
    --blue-light: #bbdefb;    /* Very Light Blue (e.g., Material Blue 100) */
    --blue-lighter: #e3f2fd;  /* Even Lighter Blue (e.g., Material Blue 50) */
    --blue-subtle: #90caf9;   /* Muted Blue (e.g., Material Blue 200) */
}

.bg-blue-dark { background-color: var(--blue-dark) !important; }
.bg-blue-main { background-color: var(--blue-main) !important; }
.bg-blue-accent { background-color: var(--blue-accent) !important; }
.bg-blue-light { background-color: var(--blue-light) !important; }
.bg-blue-lighter { background-color: var(--blue-lighter) !important; }
.bg-blue-subtle { background-color: var(--blue-subtle) !important; }

.text-blue-dark { color: var(--blue-dark) !important; }
.text-blue-main { color: var(--blue-main) !important; }
.text-blue-accent { color: var(--blue-accent) !important; }
.text-blue-light { color: var(--blue-light) !important; }
.text-blue-lighter { color: var(--blue-lighter) !important; }
.text-blue-subtle { color: var(--blue-subtle) !important; }

/* Custom Gradient untuk card header */
.bg-gradient-blue {
    background: linear-gradient(45deg, var(--blue-dark) 0%, var(--blue-main) 100%) !important;
}

.rounded-4 {
    border-radius: 1.25rem !important;
}

.rounded-3 {
    border-radius: 1rem !important;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 1.2rem;
}

.class-item .card-body {
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.class-item:hover .card-body {
    background-color: var(--blue-lighter) !important; /* Warna hover biru muda */
    transform: translateX(5px); /* Sedikit geser ke kanan */
}

.class-item .card-body h5 {
    transition: color 0.3s ease;
}

.class-item:hover .card-body h5 {
    color: var(--blue-dark) !important; /* Teks nama kelas jadi biru gelap saat hover */
}

.class-item:hover .card-body .fa-chevron-right {
    transform: translateX(5px);
    transition: transform 0.3s ease;
}

/* Mengatur alert info agar lebih menarik */
.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
</style>
@endsection