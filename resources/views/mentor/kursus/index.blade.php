@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <div class="">
    <h3 class="fw-bold py-3 mb-1 mt-2">Daftar Kursus Anda</h3>
    <div class="card shadow-sm p-4"> 
        

        <!-- Pencarian dan Filter -->
        <form method="GET" action="{{ route('mentor.kursus.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari kursus...">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request()->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request()->status == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tabel Kursus -->
        <table class="table table-bordered">
            <thead>
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
                @foreach ($courses as $course)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $course->nama_kelas }}</td>
                    <td>{{ $course->kategori->nama_kategori ?? 'N/A' }}</td>
                    <td>{{ $course->mentor->name ?? 'N/A' }}</td>
                    <td>
                        @if ($course->status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        @if ($course->status == 'Aktif')
                            <a href="{{ route('mentor.kursus.show', $course->id) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                        @else
                            <span class="text-muted">Kursus Terkunci</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
    </div>
</div>
@endsection
