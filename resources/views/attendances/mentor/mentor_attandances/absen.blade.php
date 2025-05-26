@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <h2>Absen Mengajar</h2>
    <p><strong>Kursus:</strong> {{ $course->nama }}</p>
    <p><strong>Pertemuan:</strong> {{ $meeting->judul }} (Ke-{{ $meeting->pertemuan }})</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('mentor.absen.store') }}" method="POST">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">
        <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
        <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::today()->toDateString() }}">

        <div class="form-group mt-3">
            <label for="status">Status Kehadiran</label>
            <select name="status" id="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="Hadir">Hadir</option>
                <option value="Tidak Hadir">Tidak Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>
            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group mt-3" id="waktu-absen-group" style="display: none;">
            <label for="waktu_absen">Waktu Mengajar</label>
            <input type="datetime-local" class="form-control" name="waktu_absen" id="waktu_absen" 
            value="{{ old('waktu_absen', now()->format('Y-m-d\TH:i')) }}">
            @error('waktu_absen')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan Absen</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const statusSelect = document.getElementById('status');
        const waktuAbsenGroup = document.getElementById('waktu-absen-group');

        function toggleWaktuAbsen() {
            if (statusSelect.value === 'Hadir') {
                waktuAbsenGroup.style.display = 'block';
            } else {
                waktuAbsenGroup.style.display = 'none';
            }
        }

        statusSelect.addEventListener('change', toggleWaktuAbsen);

        // Jalankan saat halaman pertama kali dibuka
        toggleWaktuAbsen();
    });
</script>
@endsection
