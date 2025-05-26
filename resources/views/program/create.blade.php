@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Tambah Program</h2>

        <form action="{{ route('program.store') }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
                <label for="nama_program" class="form-label">Nama Program</label>
                <input type="text" name="nama_program" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('program.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
