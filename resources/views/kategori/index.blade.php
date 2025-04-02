@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Kategori</h2>
        <a href="{{ route('kategori.create') }}" class="btn btn-primary mb-3">Tambah Kategori</a>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kategoris as $index => $kategori)
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
                                <del>{{ Str::limit($kategori->description, 50) }}</del>
                            @else
                                {{ Str::limit($kategori->description, 50) }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kategori.show', $kategori->id) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            @if($kategori->deleted_at)
                                <form action="{{ route('kategori.restore', $kategori->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $kategoris->links() }}
    </div>
@endsection
