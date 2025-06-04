@extends('layouts.peserta.template')

@section('content')
<div class="container py-4"> {{-- Added vertical padding for better spacing --}}
    <div class="mb-4 d-flex align-items-center"> {{-- Flexbox for title and potential back button --}}
        <a href="{{ route('courses.indexpeserta') }}" class="btn btn-outline-secondary me-3 rounded-pill"> {{-- Back button --}}
            <i class="bi bi-arrow-left"></i> <span class="d-none d-md-inline">Kembali ke Daftar Kursus</span>
        </a>
        <h3 class="mb-0 fw-bold text-primary">{{ $course->nama_kelas }}</h3> {{-- Make title more prominent --}}
    </div>

    {{-- Include the improved tab navigation --}}
    @include('peserta.kursus.partials.nav-tabs', ['activeTab' => 'meetings'])

    <div class="card mb-4 shadow-lg border-0 animated fadeIn"> {{-- Stronger shadow, no border, added animation --}}
        <div class="card-header bg-primary text-white d-flex align-items-center py-3 px-4"> {{-- Darker header, increased padding --}}
            <i class="bi bi-journal-check me-3 fs-5"></i> {{-- New icon for learning materials --}}
            <h5 class="mb-0 fw-bold text-white">Materi Pembelajaran</h5>
        </div>
        <div class="card-body p-4"> {{-- Increased padding --}}
            @if($course->lessons && $course->lessons->count() > 0) {{-- Use count() for collections --}}
                <div class="list-group list-group-flush"> {{-- Use list-group-flush for borderless items --}}
                    @foreach ($course->lessons as $lesson)
                        <div class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 px-0 border-bottom"> {{-- Flex column on mobile, row on md+ --}}
                            <div class="mb-2 mb-md-0 d-flex align-items-center">
                                <span class="badge bg-primary text-white rounded-pill me-3 py-2 px-3 fw-bold flex-shrink-0">
                                    Pertemuan {{ $lesson->meeting->pertemuan ?? 'N/A' }}
                                </span>
                                <h6 class="mb-0 text-dark fw-semibold flex-grow-1">
                                    {{ $lesson->meeting->judul ?? $lesson->judul }}
                                </h6>
                            </div>
                            <div class="text-end"> {{-- Align action button to end --}}
                                <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-info btn-sm text-white rounded-pill px-3 py-2"> {{-- Rounded button --}}
                                    <i class="bi bi-eye me-2"></i>Lihat Materi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info text-center py-4 rounded-3"> {{-- Improved alert styling --}}
                    <i class="bi bi-info-circle-fill fs-3 mb-2"></i>
                    <h4>Belum ada materi tersedia untuk kursus ini.</h4>
                    <p class="mb-0">Mohon cek kembali nanti atau hubungi pengajar Anda.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Animasi */
    .animated.fadeIn {
        animation: fadeIn 0.8s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Card Styling */
    .card {
        border-radius: 0.75rem; /* Consistent rounded corners */
    }

    .card-header.bg-primary {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }

    /* List Group Item Enhancements */
    .list-group-item {
        background-color: #f8f9fa; /* Light background for items */
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .list-group-item:hover {
        background-color: #e9ecef; /* Darker background on hover */
        transform: translateY(-2px); /* Subtle lift effect */
        box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* Subtle shadow on hover */
    }

    .list-group-item .badge {
        min-width: 90px; /* Ensure badge has consistent width */
        text-align: center;
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) { /* Small devices (phones) */
        .mb-md-0 { margin-bottom: 1rem !important; } /* Add margin bottom for flex-column items */
        .px-0 { padding-left: 1rem !important; padding-right: 1rem !important; } /* Restore horizontal padding for items */
        .list-group-item .text-end {
            width: 100%; /* Make button full width on mobile */
            text-align: center !important; /* Center button on mobile */
        }
        .btn.rounded-pill {
            width: 80%; /* Adjust button width */
            max-width: 250px; /* Max width for button */
        }
        .btn.rounded-pill .me-2 { /* Adjust icon margin */
            margin-right: 0.5rem !important;
        }
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .btn-outline-secondary .d-md-inline { /* Hide text for back button on mobile */
            display: none !important;
        }
        .btn-outline-secondary { /* Adjust back button size on mobile */
            padding: 0.5rem 0.75rem;
        }
        .fs-5 { font-size: 1.25rem !important; } /* Adjust icon size in header */
        .me-3 { margin-right: 0.75rem !important; } /* Adjust icon margin in header */
    }
</style>
@endpush
@endsection