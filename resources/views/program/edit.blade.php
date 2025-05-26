@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Edit Program</h2>

        <form action="{{ route('program.update', $program->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_program" class="form-label">Nama Program</label>
                <input type="text" name="nama_program" class="form-control" value="{{ $program->nama_program }}" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control">{{ $program->deskripsi }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('program.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
