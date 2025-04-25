@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h3 class="fw-bold py-3 mb-1 mt-2">Daftar Sertifikat</h3>
    <div class="card shadow-sm p-4">
        <!-- Menampilkan Pesan Sukses atau Info -->
        @if(session('errors'))
            <div class="alert alert-danger">
                <ul>
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
        <div class="mb-3">
            <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Sertifikat Baru
            </a>
        </div>

        <!-- Pencarian dan Filter -->
        <form method="GET" action="{{ route('certificates.indexAdmin') }}" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari sertifikat...">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Diterbitkan" {{ request()->status == 'Diterbitkan' ? 'selected' : '' }}>Diterbitkan</option>
                        <option value="Belum Diterbitkan" {{ request()->status == 'Belum Diterbitkan' ? 'selected' : '' }}>Belum Diterbitkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Tabel Sertifikat -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Peserta</th>
                    <th>Kode Sertifikat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($certificates as $certificate)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $certificate->user->name }}</td>
                    <td>{{ $certificate->kode_sertifikat }}</td>
                    <td>
                        @if ($certificate->status == 'Diterbitkan')
                            <span class="badge bg-success">Diterbitkan</span>
                        @else
                            <span class="badge bg-warning">Belum Diterbitkan</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $certificates->links() }}
    </div>
</div>
@endsection
