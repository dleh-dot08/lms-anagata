@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Detail Kategori</h2>

        <div class="mb-3">
            <strong>Nama Kategori:</strong>
            <p>
                @if ($kategori->deleted_at)
                    <del>{{ $kategori->nama_kategori }}</del>
                @else
                    {{ $kategori->nama_kategori }}
                @endif
            </p>
        </div>

        <div class="mb-3">
            <strong>Deskripsi:</strong>
            <p>
                @if ($kategori->deleted_at)
                    <del>{{ $kategori->description ?? 'Tidak ada deskripsi' }}</del>
                @else
                    {{ $kategori->description ?? 'Tidak ada deskripsi' }}
                @endif
            </p>
        </div>

        <div class="mt-4">
            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning">Edit</a>

            @if($kategori->deleted_at)
                <form action="{{ route('kategori.restore', $kategori->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Restore</button>
                </form>
            @else
                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            @endif
        </div>
    </div>
@endsection
