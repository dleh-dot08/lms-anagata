@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <div class="py-3">
        <h3 class="fw-bold mb-3">📚 Daftar Kursus Anda</h3>

        <div class="card shadow-sm p-4">

            <!-- Pencarian dan Filter -->
            <form method="GET" action="{{ route('mentor.kursus.index') }}" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="search" value="{{ request()->search }}"
                            class="form-control" placeholder="🔍 Cari berdasarkan nama kursus atau kategori...">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">📂 Semua Status</option>
                            <option value="aktif" {{ request()->status == 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="tidak_aktif" {{ request()->status == 'tidak_aktif' ? 'selected' : '' }}>❌ Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter-alt"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabel Kursus -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Kursus</th>
                            <th>Kategori</th>
                            <th>Mentor</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->nama_kelas }}</td>
                                <td>{{ $course->kategori->nama_kategori ?? '—' }}</td>
                                <td>{{ $course->mentor->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $course->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $course->status }}
                                    </span>
                                </td>
                                <td>
                                    @if ($course->status == 'Aktif')
                                        <a href="{{ route('mentor.kursus.show', $course->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="bx bx-show"></i> Detail
                                        </a>
                                    @else
                                        <span class="text-muted"><i class="bx bx-lock"></i> Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Tidak ada kursus ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
    </div>
</div>
@endsection
