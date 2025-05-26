@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Program</h2>
                    <a href="{{ route('program.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="mb-3">
                    <h5>Nama Program:</h5>
                    <p class="fw-bold">{{ $program->nama_program }}</p>
                </div>

                <div class="mb-3">
                    <h5>Deskripsi:</h5>
                    <p>{{ $program->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>

                <div class="mb-3">
                    <h5>Status:</h5>
                    @if ($program->deleted_at)
                        <span class="badge bg-danger">Dihapus</span>
                    @else
                        <span class="badge bg-success">Aktif</span>
                    @endif
                </div>

                <div class="mb-3">
                    <h5>Dihapus Pada:</h5>
                    <p>{{ $program->deleted_at ?? 'Belum Pernah Dihapus' }}</p>
                </div>

                <div class="mt-4">
                    @if($program->deleted_at)
                        <form action="{{ route('program.restore', $program->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Restore</button>
                        </form>
                    @else
                        <a href="{{ route('program.edit', $program->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('program.destroy', $program->id) }}" method="POST" class="d-inline">
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
