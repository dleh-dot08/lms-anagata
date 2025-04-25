@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Tambah Sertifikat untuk Kursus: {{ $course->nama_kelas }}</h2>

    <form method="POST" action="{{ route('certificates.store') }}">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <div class="mb-3">
            <label for="kode_sertifikat" class="form-label">Kode Sertifikat</label>
            <input type="text" class="form-control" id="kode_sertifikat" name="kode_sertifikat" required>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Pilih Peserta</label>
            <select class="form-control" name="user_id" id="user_id" required>
                <option value="">Pilih Peserta</option>
                @foreach($participants as $participant)
                    <option value="{{ $participant->user_id }}">{{ $participant->user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="file_sertifikat" class="form-label">File Sertifikat</label>
            <input type="file" class="form-control" name="file_sertifikat" id="file_sertifikat" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
            <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status Sertifikat</label>
            <select class="form-control" name="status" id="status" required>
                <option value="Diterbitkan">Diterbitkan</option>
                <option value="Belum Diterbitkan">Belum Diterbitkan</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Buat Sertifikat</button>
    </form>
</div>
@endsection
