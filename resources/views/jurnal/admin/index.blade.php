@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-transparent" style="background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%) !important;">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="bx bx-book-open fs-2 text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold">Catatan Mentor</h3>
                            <p class="mb-0 opacity-80">Pantau catatan pembelajaran yang dibuat oleh mentor</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('admin.notes.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari kursus..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
    </div>

    <!-- Course List -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($courses as $course)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm hover-shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                <i class="bx bx-book text-white fs-4"></i>
                            </div>
                            <h5 class="card-title mb-0 fw-bold">{{ $course->nama_kelas }}</h5>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">
                                <i class="bx bx-calendar me-1"></i> 
                                {{ \Carbon\Carbon::parse($course->waktu_mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($course->waktu_akhir)->format('d M Y') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="bx bx-user me-1"></i> 
                                Mentor: {{ $course->mentor->name ?? 'Belum ditentukan' }}
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ $course->meetings->count() }} Pertemuan</span>
                            <a href="{{ route('admin.notes.meetings', $course->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bx bx-list-ul me-1"></i> Lihat Catatan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="bx bx-info-circle text-secondary" style="font-size: 4rem;"></i>
                        </div>
                        <h5>Tidak Ada Kursus</h5>
                        <p class="text-muted">Belum ada kursus yang tersedia saat ini.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $courses->onEachSide(1)->links('pagination.custom') }}
    </div>
</div>

<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection