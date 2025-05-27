@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4 bg-light min-vh-100"> {{-- Menggunakan bg-light dan min-vh-100 untuk latar belakang --}}
    <div class="mb-5">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-gradient-primary p-3 rounded-3 shadow-sm"> {{-- Menggunakan kelas Bootstrap untuk warna dan shadow --}}
                {{-- SVG Icon: Ukuran disesuaikan dengan kelas Bootstrap --}}
                <svg class="bi text-white" width="32" height="32" fill="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="ms-4">
                <h1 class="h3 fw-bold text-dark mb-0">Kursus yang Anda Ampu</h1> {{-- Heading Bootstrap --}}
                <p class="text-muted mt-1">Kelola dan pantau progres kelas Anda</p>
            </div>
        </div>
        
        <div class="row g-4 mb-5"> {{-- Menggunakan row dan g-4 untuk gap --}}
            <div class="col-md-4"> {{-- Kolom untuk layout responsive --}}
                <div class="card shadow-sm border-0 h-100"> {{-- Card Bootstrap --}}
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle p-3 rounded-3 flex-shrink-0"> {{-- Warna background disesuaikan --}}
                                {{-- SVG Icon --}}
                                <svg class="bi text-primary" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-muted small fw-medium mb-1">Total Kursus</p>
                                <p class="h2 fw-bold text-dark">{{ $courses->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle p-3 rounded-3 flex-shrink-0">
                                {{-- SVG Icon --}}
                                <svg class="bi text-success" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-muted small fw-medium mb-1">Kursus Aktif</p>
                                <p class="h2 fw-bold text-dark">{{ $courses->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-purple-subtle p-3 rounded-3 flex-shrink-0"> {{-- Perlu custom CSS untuk purple-subtle jika tidak ada di Bootstrap --}}
                                {{-- SVG Icon --}}
                                <svg class="bi text-purple" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"> {{-- Perlu custom CSS untuk text-purple --}}
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-muted small fw-medium mb-1">Laporan Siap</p>
                                <p class="h2 fw-bold text-dark">{{ $courses->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($courses->isNotEmpty())
        <div class="row g-4">
            @foreach ($courses as $course)
                <div class="col-md-6 col-lg-4"> {{-- Kolom responsive untuk card kursus --}}
                    <div class="card shadow-lg border-0 h-100 overflow-hidden"> {{-- Card untuk setiap kursus --}}
                        <div class="card-header bg-gradient-primary text-white p-4 position-relative overflow-hidden">
                            <div class="position-absolute top-0 end-0 w-25 h-100 bg-white opacity-10 rounded-circle translate-middle-x mt-n4 me-n4"></div> {{-- Efek lingkaran --}}
                            <div class="position-relative z-1">
                                <div class="d-flex align-items-center mb-2">
                                    <svg class="bi text-white me-2" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="small fw-medium opacity-75">Kursus</span>
                                </div>
                                <h3 class="h5 fw-bold mb-2 text-truncate-2 text-white">{{ $course->nama_kelas }}</h3> {{-- text-truncate-2 perlu custom CSS --}}
                                <div class="d-flex align-items-center small opacity-75">
                                    <svg class="bi text-white me-1" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Aktif</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex align-items-center text-muted mb-2">
                                    <svg class="bi text-secondary me-2" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="small">ID: {{ $course->id }}</span>
                                </div>
                                
                                <div class="d-flex align-items-center text-muted">
                                    <svg class="bi text-secondary me-2" width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="small">Status: <span class="text-success fw-medium">Aktif</span></span>
                                </div>
                            </div>

                            <div class="pt-4 border-top">
                                <a href="{{ route('mentor.score.showReport', $course->id) }}" 
                                   class="btn btn-primary w-100 d-flex align-items-center justify-content-center py-2 px-3 shadow">
                                    <svg class="bi text-white me-2" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Lihat Laporan Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @else
        <div class="card shadow-sm border-0 p-5 text-center">
            <div class="mx-auto" style="max-width: 400px;">
                <div class="mb-4">
                    <svg class="bi text-secondary mx-auto" width="96" height="96" fill="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                
                <h3 class="h4 fw-bold text-dark mb-3">Belum Ada Kursus</h3>
                <p class="text-muted mb-4">Anda belum mengampu kursus apapun. Hubungi administrator untuk mendapatkan akses kursus.</p>
                
                <div class="d-grid gap-3"> {{-- d-grid gap-3 untuk tombol vertikal --}}
                    <button class="btn btn-primary d-flex align-items-center justify-content-center py-2 px-3 shadow">
                        <svg class="bi text-white me-2" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Hubungi Administrator
                    </button>
                    
                    <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center py-2 px-3">
                        <svg class="bi text-secondary me-2" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Halaman
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Custom CSS untuk beberapa gaya yang tidak ada di Bootstrap --}}
<style>
    /* Gradient primer untuk card-header dan main header */
    .bg-gradient-primary {
        background: linear-gradient(to right, #2563eb, #6d28d9); /* blue-600 to indigo-600 */
    }

    /* Warna khusus untuk purple-subtle dan text-purple */
    .bg-purple-subtle {
        background-color: #ede9fe; /* setara dengan bg-purple-100 */
    }
    .text-purple {
        color: #7c3aed; /* setara dengan text-purple-600 */
    }

    /* Untuk line-clamp-2 (truncate multi-line) */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Efek hover untuk card kursus (opsional, karena Bootstrap tidak punya efek scale secara default) */
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease-in-out;
    }
    .card:hover .shadow-lg { /* Mengubah shadow saat hover */
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }

    /* Untuk tombol aksi cepat */
    .btn.btn-light {
        border: 1px solid rgba(0,0,0,.125); /* Border agar terlihat jelas */
    }
    .btn.btn-light:hover {
        background-color: #e2e6ea; /* Hover state untuk tombol light */
    }
    .btn.btn-light svg {
        transition: transform 0.2s ease-in-out;
    }
    .btn.btn-light:hover svg {
        transform: scale(1.1);
    }
</style>
@endsection