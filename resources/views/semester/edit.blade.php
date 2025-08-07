@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h2>Edit Semester</h2>
    <form action="{{ route('semester.update', $semester->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nama Semester</label>
            <input type="text" name="name" class="form-control" value="{{ $semester->name }}" required>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Tahun</label>
            <input type="text" name="year" class="form-control" value="{{ $semester->year }}" required>
        </div>
        <div class="mb-3">
            <label for="is_active" class="form-label">Status Aktif</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $semester->is_active ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$semester->is_active ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('semester.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection