@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Laporan /</span> Data Nilai Kursus
        </h4>
    </div>
    <div class="mb-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>
    <!-- Search and Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reports.nilai.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Kursus/Sekolah</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari nama kursus atau sekolah..." 
                               name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter Sekolah</label>
                    <select class="form-select" name="sekolah_id" onchange="this.form.submit()">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolah as $s)
                            <option value="{{ $s->id }}" {{ request('sekolah_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_sekolah }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('search') || request('sekolah_id'))
                    <div class="col-md-4 d-flex align-items-end">
                        <a href="{{ route('admin.reports.nilai.index') }}" class="btn btn-secondary">
                            <i class="bx bx-reset me-1"></i> Reset Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Kursus</th>
                            <th>Nama Sekolah</th>
                            <th>Mentor</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $index => $course)
                            <tr>
                                <td style="width: 1%">{{ $index + 1 }}</td>
                                <td style="width: 30%">
                                    <div class="d-flex flex-column">
                                        {{ $course->nama_kelas }}
                                    </div>
                                </td>
                                <td style="width: 20%">
                                    <div class="d-flex flex-column">
                                        {{ $course->sekolah->nama_sekolah ?? 'Tidak ada sekolah' }}
                                    </div>
                                </td>
                                <td style="width: 15%">
                                    <div class="d-flex flex-column">
                                        {{ $course->mentor->name ?? 'Tidak ada mentor' }}
                                    </div>
                                </td>
                                <td style="width: 15%">
                                    <div class="d-flex flex-column">
                                        {{ $course->kategori->nama_kategori ?? 'Tidak ada kategori' }}
                                    </div>
                                </td>
                                <td style="width: 19%">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.reports.nilai.show', $course->id) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </a>
                                        <a href="{{ route('admin.reports.nilai.edit', $course->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bx bx-edit me-1"></i> Input Nilai
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kursus</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $courses->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection 