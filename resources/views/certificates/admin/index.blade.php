@extends('layouts.admin.template')

@section('content')
<div class="container px-3 px-md-4">
    <h3 class="fw-bold py-3 mb-1 mt-2 h5 h-md-3">Daftar Sertifikat</h3>
    <div class="card shadow-sm p-4">
        <!-- Menampilkan Pesan Sukses atau Info -->
        @if(session('errors'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tombol untuk menambah sertifikat -->
        <div class="mb-3 text-center text-md-start">
            <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Sertifikat Baru
            </a>
        </div>

        <!-- Pencarian dan Filter -->
        <form method="GET" action="{{ route('certificates.indexAdmin') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari sertifikat...">
                </div>
                <div class="col-12 col-md-4">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Diterbitkan" {{ request()->status == 'Diterbitkan' ? 'selected' : '' }}>Diterbitkan</option>
                        <option value="Belum Diterbitkan" {{ request()->status == 'Belum Diterbitkan' ? 'selected' : '' }}>Belum Diterbitkan</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tabel Sertifikat -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Kode Sertifikat</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificates as $certificate)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $certificate->user->name }}</td>
                        <td>{{ $certificate->kode_sertifikat }}</td>
                        <td>
                            @if ($certificate->status == 'Diterbitkan')
                                <span class="badge bg-success">Diterbitkan</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Diterbitkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('certificates.showAdmin', $certificate->id) }}" class="btn btn-info btn-sm mb-1">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            <a href="{{ route('certificates.edit', $certificate->id) }}" class="btn btn-warning btn-sm mb-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada sertifikat ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $certificates->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
</div>
@endsection
