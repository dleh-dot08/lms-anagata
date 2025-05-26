@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Laporan /</span> Nilai
    </h4>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sekolah.reports.nilai.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari nama kelas atau mentor..." 
                               name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Course List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Mentor</th>
                            <th>Kategori</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($courses as $course)
                            <tr>
                                <td><strong>{{ $course->nama_kelas }}</strong></td>
                                <td>{{ $course->mentor->name ?? '-' }}</td>
                                <td>{{ $course->kategori->nama_kategori ?? '-' }}</td>
                                <td>{{ $course->program->nama_program ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $course->status == 'Aktif' ? 'success' : 'warning' }}">
                                        {{ $course->status }}
                                    </span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($course->waktu_mulai)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($course->waktu_akhir)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('sekolah.reports.nilai.show', $course->id) }}" 
                                       class="btn btn-info btn-sm">
                                        <i class="bx bx-show me-1"></i> Detail Nilai
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data kursus</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection