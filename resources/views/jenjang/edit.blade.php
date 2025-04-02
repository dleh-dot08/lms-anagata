@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Edit Jenjang</h2>

        <form action="{{ route('jenjang.update', $jenjang->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_jenjang" class="form-label">Nama Jenjang</label>
                <input type="text" name="nama_jenjang" class="form-control" value="{{ $jenjang->nama_jenjang }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" class="form-control">{{ $jenjang->description }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('jenjang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection
