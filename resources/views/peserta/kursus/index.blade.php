@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h4 class="my-4">Kursus Saya</h4>

    <!-- Form Search & Filter -->
    <form method="GET" action="{{ route('courses.indexpeserta') }}" class="mb-4 row g-3 align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Cari Nama Kursus</label>
            <input type="text" name="search" id="search" class="form-control"
                placeholder="Contoh: Laravel Dasar" value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <label for="status" class="form-label">Filter Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </div>
    </form>

    @if($courses->count())
        <div class="row">
            @foreach($courses as $course)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->nama_kelas }}</h5>
                            <p class="card-text">{{ Str::limit($course->deskripsi, 100) }}</p>
                            <p><strong>Mentor:</strong> {{ $course->mentor->name ?? '-' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $course->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </p>
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary btn-sm">Lihat Kursus</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $courses->links() }}
        </div>
    @else
        <p class="text-muted">Tidak ada kursus yang ditemukan.</p>
    @endif
</div>
@endsection
