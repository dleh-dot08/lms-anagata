@extends('layouts.admin.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold py-3 mb-1 mt-2">Edit Sertifikat</h3>

        <!-- Menampilkan Pesan Sukses atau Info -->
        @if(session('errors'))
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Edit Sertifikat -->
        <form method="POST" action="{{ route('certificates.update', $certificate->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="user_id" class="form-label">Nama Peserta</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Pilih Peserta</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $certificate->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipe Sertifikat</label>
                <select id="type" name="type" class="form-control" onchange="toggleCourseOrActivity()">
                    <option value="course" {{ $certificate->type == 'course' ? 'selected' : '' }}>Course</option>
                    <option value="activity" {{ $certificate->type == 'activity' ? 'selected' : '' }}>Activity</option>
                </select>
            </div>

            <div class="mb-3" id="course-container" style="{{ $certificate->type == 'activity' ? 'display: none;' : '' }}">
                <label for="course_id" class="form-label">Pilih Kursus</label>
                <select name="course_id" id="course_id" class="form-control">
                    <option value="">Pilih Kursus</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $certificate->course_id == $course->id ? 'selected' : '' }}>{{ $course->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="activity-container" style="{{ $certificate->type == 'course' ? 'display: none;' : '' }}">
                <label for="activities_id" class="form-label">Pilih Aktivitas</label>
                <select name="activities_id" id="activities_id" class="form-control">
                    <option value="">Pilih Aktivitas</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}" {{ $certificate->activities_id == $activity->id ? 'selected' : '' }}>{{ $activity->nama_aktivitas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="kode_sertifikat" class="form-label">Kode Sertifikat</label>
                <input type="text" name="kode_sertifikat" class="form-control" value="{{ $certificate->kode_sertifikat }}" required>
            </div>

            <div class="mb-3">
                <label for="file_sertifikat" class="form-label">File Sertifikat</label>
                <input type="file" name="file_sertifikat" class="form-control">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file sertifikat.</small>
            </div>

            <div class="mb-3">
                <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit" class="form-control" value="{{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('Y-m-d') }}" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="Diterbitkan" {{ $certificate->status == 'Diterbitkan' ? 'selected' : '' }}>Diterbitkan</option>
