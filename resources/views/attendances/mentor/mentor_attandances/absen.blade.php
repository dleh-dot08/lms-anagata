@extends('layouts.mentor.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-calendar-check me-2"></i>Absen Mengajar</h5>
            <small class="text-muted float-end">Form Kehadiran Mentor</small>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <p><i class="bx bx-book-open text-primary me-1"></i> <strong>Kursus:</strong> {{ $course->nama }}</p>
                <p><i class="bx bx-calendar text-primary me-1"></i> <strong>Pertemuan:</strong> {{ $meeting->judul }} (Ke-{{ $meeting->pertemuan }})</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}</div>
            @elseif (session('warning'))
                <div class="alert alert-warning"><i class="bx bx-info-circle me-1"></i> {{ session('warning') }}</div>
            @elseif (session('error'))
                <div class="alert alert-danger"><i class="bx bx-error-circle me-1"></i> {{ session('error') }}</div>
            @endif

            <form action="{{ route('mentor.absen.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::today()->toDateString() }}">

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status"><i class="bx bx-user-check me-1"></i> Status Kehadiran</label>
                    <div class="col-sm-10">
                        <select name="status" id="status" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                        @error('status')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3" id="waktu-absen-group" style="display: none;">
                    <label class="col-sm-2 col-form-label" for="waktu_absen"><i class="bx bx-time me-1"></i> Waktu Mengajar</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" class="form-control" name="waktu_absen" id="waktu_absen" 
                        value="{{ old('waktu_absen', now()->format('Y-m-d\\TH:i')) }}">
                        @error('waktu_absen')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Absen</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ms-2"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
