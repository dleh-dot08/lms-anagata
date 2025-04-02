@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Jenjang</h2>
            <a href="{{ route('jenjang.create') }}" class="btn btn-primary">+ Tambah Jenjang</a>
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
                            <th>Nama Jenjang</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenjangs as $index => $jenjang)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($jenjang->deleted_at)
                                        <del>{{ $jenjang->nama_jenjang }}</del>
                                    @else
                                        {{ $jenjang->nama_jenjang }}
                                    @endif
                                </td>
                                <td>
                                    @if ($jenjang->deleted_at)
                                        <del>{{ Str::limit($jenjang->description, 50, '...') }}</del>
                                    @else
                                        {{ Str::limit($jenjang->description, 50, '...') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($jenjang->deleted_at)
                                        <span class="badge bg-danger">Dihapus</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('jenjang.show', $jenjang->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    @if(!$jenjang->deleted_at)
                                        <a href="{{ route('jenjang.edit', $jenjang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('jenjang.destroy', $jenjang->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @else
                                        <form action="{{ route('jenjang.restore', $jenjang->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data jenjang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $jenjangs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
