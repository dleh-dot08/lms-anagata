@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-primary">{{ $course->nama_kelas }}</h2>
            <p class="text-muted small mt-1">Halaman Penilaian Kursus</p>
        </div>
        <!-- Tombol Rekap Nilai -->
        <a href="{{ route('mentor.scores.recap', $course->id) }}" class="btn btn-success btn-lg shadow-sm">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> Rekap Nilai
        </a>
    </div>

    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'scores'])

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Daftar Pertemuan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($course->meetings->sortBy('pertemuan') as $meeting)
                            <div class="list-group-item p-0 border-0">
                                <div class="card border-0 mb-2 meeting-card" style="transition: all 0.3s ease;">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center mb-3 mb-md-0">
                                                <div class="bg-primary text-white rounded-circle p-3 d-inline-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                                                    <h4 class="mb-0">{{ $meeting->pertemuan }}</h4>
                                                </div>
                                            </div>
                                            <div class="col-md-8 mb-3 mb-md-0">
                                                <h5 class="card-title mb-1 fw-bold">{{ $meeting->judul }}</h5>
                                                <p class="card-text mb-0">
                                                    <i class="bi bi-calendar3 me-2 text-muted"></i>
                                                    {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d M Y') }}
                                                    @if($meeting->waktu_mulai && $meeting->waktu_selesai)
                                                        <span class="ms-2"><i class="bi bi-clock me-1 text-muted"></i>
                                                        {{ \Carbon\Carbon::parse($meeting->waktu_mulai)->format('H.i') }} - 
                                                        {{ \Carbon\Carbon::parse($meeting->waktu_selesai)->format('H.i') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-3 text-md-end">
                                                <a href="{{ route('kursus.pertemuan.show', [$course->id, $meeting->id]) }}" class="btn btn-outline-secondary me-2">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                                <a href="{{ route('mentor.scores.input', ['course' => $course->id, 'meeting' => $meeting->id]) }}" class="btn btn-primary">
                                                    <i class="bi bi-pencil-square me-1"></i> Nilai
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3">Belum ada pertemuan yang tersedia.</p>
                                <p class="small text-muted">Pertemuan akan muncul di sini setelah dijadwalkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .meeting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        background-color: #f8f9fa;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
</style>
@endsection
