@extends('layouts.peserta.template')

@section('content')
<div class="container py-4"> {{-- Add vertical padding to the container --}}
    <div class="mb-4"> {{-- Added margin-bottom for spacing --}}
        <h3 class="fw-bold py-3 mb-1 mt-2 text-primary">Daftar Kursus Anda</h3>
        <p class="text-muted">Kelola dan jelajahi kursus yang Anda ikuti.</p>
    </div>

    <div class="card shadow-lg p-md-4 p-3 animated fadeIn"> {{-- Increased shadow, added responsive padding and animation --}}

        @if(session('errors'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2"> {{-- Flexbox for button and search/filter on larger screens, wraps on smaller --}}
            <a href="{{ route('courses.joinPeserta') }}" class="btn btn-success btn-lg"> {{-- Larger button for easier tapping --}}
                <i class="bi bi-door-open me-2"></i> Gabung Kursus Baru
            </a>

            <form method="GET" action="{{ route('courses.indexpeserta') }}" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-md-end"> {{-- Flex-grow to fill space, justify-end on md --}}
                <div class="flex-grow-1 me-md-2" style="min-width: 180px;"> {{-- Allow search to grow, min-width for mobile --}}
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari kursus...">
                </div>
                <div class="" style="min-width: 150px;"> {{-- Min-width for select on mobile --}}
                    <select name="status" class="form-select"> {{-- Use form-select for better styling --}}
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request()->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request()->status == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->has('search') || request()->has('status') && request()->status != '')
                        <a href="{{ route('courses.indexpeserta') }}" class="btn btn-secondary ms-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        @if($courses->isEmpty())
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle fs-3 mb-2"></i>
                <h4>Tidak ada kursus yang ditemukan.</h4>
                <p>Silakan gabung kursus baru untuk memulai!</p>
            </div>
        @else
            <div class="table-responsive d-none d-md-block"> {{-- Hide on small screens, show on md and up --}}
                <table class="table table-hover table-striped table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col">Nama Kursus</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Mentor</th>
                            <th scope="col" style="width: 10%;">Status</th>
                            <th scope="col" style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong class="text-dark">{{ $course->nama_kelas }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $course->kategori->nama_kategori ?? 'N/A' }}</span></td>
                            <td>{{ $course->mentor->name ?? 'N/A' }}</td>
                            <td>
                                @if ($course->status == 'Aktif')
                                    <span class="badge bg-success py-2 px-3 fw-semibold">Aktif</span>
                                @else
                                    <span class="badge bg-danger py-2 px-3 fw-semibold">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if ($course->status == 'Aktif')
                                    <a href="{{ route('peserta.kursus.overview', $course->id) }}" class="btn btn-info btn-sm text-white"><i class="bi bi-eye me-1"></i> Lihat</a>
                                @else
                                    <span class="text-muted small">Kursus Terkunci</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-md-none"> {{-- Show on small screens only (mobile/tablet) --}}
                <div class="row row-cols-1 g-3"> {{-- Grid of cards for mobile --}}
                    @foreach ($courses as $course)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-start border-{{ $course->status == 'Aktif' ? 'success' : 'danger' }} border-4">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-2"><i class="bi bi-book me-2"></i> {{ $course->nama_kelas }}</h5>
                                <h6 class="card-subtitle mb-2 text-muted small"><i class="bi bi-tags me-1"></i> {{ $course->kategori->nama_kategori ?? 'N/A' }}</h6>
                                <p class="card-text mb-1"><i class="bi bi-person me-2"></i> Mentor: {{ $course->mentor->name ?? 'N/A' }}</p>
                                <p class="card-text mb-3">
                                    <i class="bi bi-hourglass me-2"></i> Status:
                                    @if ($course->status == 'Aktif')
                                        <span class="badge bg-success py-1 px-2 fw-semibold">Aktif</span>
                                    @else
                                        <span class="badge bg-danger py-1 px-2 fw-semibold">Tidak Aktif</span>
                                    @endif
                                </p>
                                <div class="text-end">
                                    @if ($course->status == 'Aktif')
                                        <a href="{{ route('peserta.kursus.overview', $course->id) }}" class="btn btn-info btn-sm text-white">
                                            <i class="bi bi-eye me-1"></i> Lihat Detail
                                        </a>
                                    @else
                                        <span class="text-muted small">Kursus Terkunci</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-center mt-4">
            {{ $courses->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
</div>

@push('css')
<style>
    /* Fade-in Animation for the main card */
    .animated.fadeIn {
        animation: fadeIn 0.8s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* General Card Styling */
    .card {
        border-radius: 0.75rem; /* Slightly more rounded corners */
    }

    /* Custom styles for mobile cards (only when d-md-none is active) */
    @media (max-width: 767.98px) { /* Applies to devices smaller than md (768px) */
        .card.h-100 {
            margin-bottom: 1rem; /* Spacing between cards in mobile view */
        }
        .card-title {
            font-size: 1.15rem; /* Adjust title size for mobile cards */
        }
        .card-text, .card-subtitle {
            font-size: 0.9rem; /* Adjust text size for mobile cards */
        }
        .btn-lg {
            width: 100%; /* Make the "Gabung Kursus" button full width on mobile */
        }
        form.d-flex.flex-wrap > div {
            flex-basis: 100%; /* Make search and filter inputs full width on mobile */
        }
        form.d-flex.flex-wrap > div:last-child {
            display: flex; /* Ensure filter and reset buttons are side-by-side */
            gap: 0.5rem;
        }
        form.d-flex.flex-wrap > div:last-child button,
        form.d-flex.flex-wrap > div:last-child a.btn {
            flex-grow: 1; /* Make buttons fill available space */
        }
    }
</style>
@endpush
@endsection