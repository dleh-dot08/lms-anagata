@extends('layouts.admin.template')

@section('content')
    <h2>Detail Kategori</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $kategori->nama_kategori }}</h5>
            <p class="card-text">{{ $kategori->description ? $kategori->description : 'Tidak ada deskripsi.' }}</p>
            
            <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-warning">Edit</a>

            <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>

            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
        </div>
    </div>

@endsection
