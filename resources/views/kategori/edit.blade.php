@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Edit Kategori</h2>
        <a href="{{ route('kategori.index') }}" class="btn btn-secondary mb-3">Kembali</a>

        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                @error('nama_kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $kategori->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Perbarui</button>
        </form>
    </div>
@endsection
