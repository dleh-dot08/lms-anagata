@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h2>Tambah Semester</h2>
    <form action="{{ route('semester.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Semester</label>
            <input type="text" name="name" class="form-control" required placeholder="Ganjil/Genap">
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Tahun</label>
            <input type="text" name="year" class="form-control" required placeholder="2024/2025">
        </div>
        <div class="mb-3">
            <label for="is_active" class="form-label">Status Aktif</label>
            <select name="is_active" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('semester.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection