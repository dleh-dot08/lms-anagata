@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h5 class="mb-4">{{ isset($meeting) ? 'Edit Pertemuan' : 'Tambah Pertemuan' }}</h5>

    <form action="{{ isset($meeting) ? route('meetings.update', [$course->id, $meeting->id]) : route('meetings.store', $course->id) }}" method="POST">
        @csrf
        @if(isset($meeting))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="judul" class="form-label">Judul Pertemuan</label>
            <input type="text" name="judul" class="form-control" id="judul" value="{{ old('judul', $meeting->judul ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Pertemuan</label>
            <textarea name="description" class="form-control" id="deskripsi" rows="4" required>{{ old('description', $meeting->description ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="pertemuan_ke" class="form-label">Pertemuan Ke</label>
            <input type="number" name="pertemuan" class="form-control" id="pertemuan_ke" value="{{ old('pertemuan', $meeting->pertemuan ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Pertemuan</label>
            <input type="date" name="tanggal_pelaksanaan" class="form-control" id="tanggal" value="{{ old('tanggal_pelakasanaan', isset($meeting->tanggal_pelaksanaan) ? $meeting->tanggal_pelaksanaan->format('Y-m-d') : '') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($meeting) ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
