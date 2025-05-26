@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Daftar Sekolah
    </h4>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.sekolah.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari nama sekolah atau alamat..." 
                               name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="jenjang_id" onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjang as $j)
                            <option value="{{ $j->id }}" {{ request('jenjang_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->nama_jenjang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.sekolah.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> Tambah Sekolah
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sekolah List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Sekolah</th>
                            <th>Jenjang</th>
                            <th>Alamat</th>
                            <th>PIC</th>
                            <th>Jumlah Peserta</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($sekolah as $s)
                            <tr>
                                <td>
                                    <strong>{{ $s->nama_sekolah }}</strong>
                                </td>
                                <td>{{ $s->jenjang->nama_jenjang }}</td>
                                <td>{{ $s->alamat }}</td>
                                <td>
                                    @if($s->pic)
                                        <span class="badge bg-label-info">{{ $s->pic->name }}</span>
                                    @else
                                        <span class="badge bg-label-warning">Belum ada PIC</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $s->peserta->count() }} Peserta</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.sekolah.show', $s->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    @if(!$s->deleted_at)
                                        <a href="{{ route('admin.sekolah.edit', $s->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.sekolah.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus sekolah ini?')">Hapus</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.sekolah.restore', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data sekolah</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $sekolah->onEachSide(1)->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection 