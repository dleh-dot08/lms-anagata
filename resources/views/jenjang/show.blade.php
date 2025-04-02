@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Jenjang</h2>
                    <a href="{{ route('jenjang.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="mb-3">
                    <h5>Nama Jenjang:</h5>
                    <p class="fw-bold">{{ $jenjang->nama_jenjang }}</p>
                </div>

                <div class="mb-3">
                    <h5>Deskripsi:</h5>
                    <p>{{ $jenjang->description ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="mb-3">
                    <h5>Status:</h5>
                    @if ($jenjang->deleted_at)
                        <span class="badge bg-danger">Dihapus</span>
                    @else
                        <span class="badge bg-success">Aktif</span>
                    @endif
                </div>

                <div class="mb-3">
                    <h5>Dihapus Pada:</h5>
                    <p>{{ $jenjang->deleted_at ?? 'Belum Pernah Dihapus' }}</p>
                </div>

                <div class="mt-4">
                    @if($jenjang->deleted_at)
                        <form action="{{ route('jenjang.restore', $jenjang->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                    @else
                        <a href="{{ route('jenjang.edit', $jenjang->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('jenjang.destroy', $jenjang->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
