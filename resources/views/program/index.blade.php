@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Program</h2>
            <a href="{{ route('program.create') }}" class="btn btn-primary">+ Tambah Program</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Program</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programs as $index => $program)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($program->deleted_at)
                                        <del>{{ $program->nama_program }}</del>
                                    @else
                                        {{ $program->nama_program }}
                                    @endif
                                </td>
                                <td>
                                    @if ($program->deleted_at)
                                        <del>{{ Str::limit($program->deskripsi, 50, '...') }}</del>
                                    @else
                                        {{ Str::limit($program->deskripsi, 50, '...') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($program->deleted_at)
                                        <span class="badge bg-danger">Dihapus</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('program.show', $program->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    @if(!$program->deleted_at)
                                        <a href="{{ route('program.edit', $program->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('program.destroy', $program->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @else
                                        <form action="{{ route('program.restore', $program->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data program.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $programs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
