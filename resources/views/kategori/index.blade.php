@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori</h2>
            <a href="{{ route('kategori.create') }}" class="btn btn-primary">+ Tambah Kategori</a>
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
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoris as $index => $kategori)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($kategori->deleted_at)
                                        <del>{{ $kategori->nama_kategori }}</del>
                                    @else
                                        {{ $kategori->nama_kategori }}
                                    @endif
                                </td>
                                <td>
                                    @if ($kategori->deleted_at)
                                        <del>{{ Str::limit($kategori->description, 50, '...') }}</del>
                                    @else
                                        {{ Str::limit($kategori->description, 50, '...') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($kategori->deleted_at)
                                        <span class="badge bg-danger">Dihapus</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kategori.show', $kategori->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    @if(!$kategori->deleted_at)
                                        <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @else
                                        <form action="{{ route('kategori.restore', $kategori->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $kategoris->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
