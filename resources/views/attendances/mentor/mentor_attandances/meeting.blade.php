@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-calendar-event fs-1 opacity-75"></i>
                        </div>
                        <div>
                            <h2 class="mb-1 fw-bold text-white">Daftar Pertemuan</h2>
                            <p class="mb-0 fs-5 opacity-90">{{ $course->nama }}</p>
                            <small class="opacity-75">
                                <i class="bi bi-collection me-1"></i>
                                {{ $meetings->count() }} Pertemuan Tersedia
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($meetings->isEmpty())
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Pertemuan</h4>
                    <p class="text-muted">Pertemuan untuk kursus ini belum tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                </div>
            </div>
        </div>
    @else
        <!-- Meetings Grid -->
        <div class="row g-4">
            @foreach($meetings as $meeting)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card border-0 shadow-sm h-100 meeting-card" style="transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <!-- Meeting Header -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="meeting-number me-3">
                                <div class="badge bg-primary rounded-circle p-2 fs-6 fw-bold" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                    {{ $meeting->pertemuan }}
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1 fw-bold text-dark">
                                    Pertemuan ke-{{ $meeting->pertemuan }}
                                </h5>
                                @if(in_array($meeting->id, $attendedMeetingIds))
                                    <small class="text-success fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Sudah Absen
                                    </small>
                                @else
                                    <small class="text-warning fw-semibold">
                                        <i class="bi bi-clock me-1"></i>
                                        Belum Absen
                                    </small>
                                @endif
                            </div>
                        </div>

                        <!-- Meeting Content -->
                        <div class="meeting-content mb-4">
                            <h6 class="fw-semibold text-dark mb-2">{{ $meeting->judul }}</h6>
                            
                            @if($meeting->tanggal ?? false)
                                <div class="d-flex align-items-center text-muted mb-2">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <small>{{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }}</small>
                                </div>
                            @endif

                            @if($meeting->waktu ?? false)
                                <div class="d-flex align-items-center text-muted mb-2">
                                    <i class="bi bi-clock me-2"></i>
                                    <small>{{ $meeting->waktu }}</small>
                                </div>
                            @endif

                            @if($meeting->lokasi ?? false)
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <small>{{ $meeting->lokasi }}</small>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <div class="mt-auto">
                            @if(in_array($meeting->id, $attendedMeetingIds))
                                <button class="btn btn-success btn-sm w-100 disabled" disabled>
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Absen Sudah Terisi
                                </button>
                            @else
                                <a href="{{ route('mentor.absen.create', [$course->id, $meeting->id]) }}"
                                   class="btn btn-primary btn-sm w-100 shadow-sm hover-lift">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Isi Absen Sekarang
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="card-footer border-0 bg-transparent p-0">
                        @if(in_array($meeting->id, $attendedMeetingIds))
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: 100%;"></div>
                            </div>
                        @else
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: 0%;"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Statistics -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-collection text-primary me-2 fs-4"></i>
                                    <div>
                                        <h5 class="mb-0 fw-bold">{{ $meetings->count() }}</h5>
                                        <small class="text-muted">Total Pertemuan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-check-circle text-success me-2 fs-4"></i>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-success">{{ count($attendedMeetingIds) }}</h5>
                                        <small class="text-muted">Sudah Absen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-clock text-warning me-2 fs-4"></i>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-warning">{{ $meetings->count() - count($attendedMeetingIds) }}</h5>
                                        <small class="text-muted">Belum Absen</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Custom Styles -->
<style>
    .meeting-card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .meeting-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    
    .hover-lift:hover {
        transform: translateY(-1px);
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .meeting-number .badge {
        font-size: 1rem;
        font-weight: 700;
    }
    
    .progress-bar {
        transition: width 0.3s ease;
    }
    
    .card-title {
        color: #2c3e50;
    }
    
    .meeting-content {
        min-height: 80px;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-1px);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #1e7e34);
        border: none;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

{{-- Bootstrap 5 tooltips initialization --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Add loading animation for cards
        const cards = document.querySelectorAll('.meeting-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>

@endsection