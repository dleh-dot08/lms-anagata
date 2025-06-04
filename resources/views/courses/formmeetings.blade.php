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
            @error('judul') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Pertemuan</label>
            <textarea name="description" class="form-control" id="deskripsi" rows="4" required>{{ old('description', $meeting->description ?? '') }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="pertemuan_ke" class="form-label">Pertemuan Ke</label>
            <input type="number" name="pertemuan" class="form-control" id="pertemuan_ke" value="{{ old('pertemuan', $meeting->pertemuan ?? '') }}" required>
            @error('pertemuan') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Pertemuan</label>
            <input type="date" name="tanggal_pelaksanaan" class="form-control" id="tanggal" value="{{ old('tanggal_pelaksanaan', isset($meeting->tanggal_pelaksanaan) ? $meeting->tanggal_pelaksanaan->format('Y-m-d') : '') }}" required>
            @error('tanggal_pelaksanaan') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="jam_mulai" class="form-label">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control" id="jam_mulai" value="{{ old('jam_mulai', isset($meeting->jam_mulai) ? $meeting->jam_mulai->format('H:i') : '') }}" required>
                @error('jam_mulai') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="jam_selesai" class="form-label">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control" id="jam_selesai" value="{{ old('jam_selesai', isset($meeting->jam_selesai) ? $meeting->jam_selesai->format('H:i') : '') }}" required>
                @error('jam_selesai') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        @if(isset($meeting)) {{-- Hanya tampilkan alasan perubahan saat edit --}}
        <div class="mb-3">
            <label for="reason" class="form-label">Alasan Perubahan Jadwal (Opsional)</label>
            <textarea name="reason" class="form-control" id="reason" rows="3">{{ old('reason') }}</textarea>
            @error('reason') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        @endif

        <button type="submit" class="btn btn-primary">
            {{ isset($meeting) ? 'Update' : 'Simpan' }}
        </button>
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection