@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Kelas</h2>
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="mb-3">
                    <h5>Nama Kelas:</h5>
                    <p class="fw-bold">{{ $kelas->nama }}</p>
                </div>

                <div class="mb-3">
                    <h5>Jenjang:</h5>
                    <p>{{ $kelas->jenjang->nama_jenjang }}</p>
                </div>

                <div class="mb-3">
                    <h5>Dibuat Pada:</h5>
                    <p>{{ $kelas->created_at->format('d F Y H:i') }}</p>
                </div>

                <div class="mb-3">
                    <h5>Terakhir Diupdate:</h5>
                    <p>{{ $kelas->updated_at->format('d F Y H:i') }}</p>
                </div>

                <div class="mb-3">
                    <h5>Dihapus Pada:</h5>
                    <p>{{ $kelas->deleted_at ? $kelas->deleted_at->format('d F Y H:i') : 'Belum Pernah Dihapus' }}</p>
                </div>

                <div class="mt-4">
                    @if($kelas->deleted_at)
                        <form action="{{ route('admin.kelas.restore', $kelas->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                    @else
                        <a href="{{ route('admin.kelas.edit', $kelas->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">Hapus</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 