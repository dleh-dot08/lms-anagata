@extends('layouts.admin.template')
@section('content')
<h1 class="text-xl font-bold mb-4">Tambah Sertifikat</h1>
<form action="{{ route('certificates.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label class="block">Nama Peserta</label>
        <select name="user_id" class="form-select">
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="block">Tipe Sertifikat</label>
        <select name="tipe" id="tipe-select" class="form-select">
            <option value="course">Course</option>
            <option value="activity">Activity</option>
        </select>
    </div>
    <div id="course-select" class="mb-3">
        <label class="block">Course</label>
        <select name="course_id" class="form-select">
            @foreach ($courses as $course)
                <option value="{{ $course->id }}">{{ $course->nama_kelas }}</option>
            @endforeach
        </select>
    </div>
    <div id="activity-select" class="mb-3 hidden">
        <label class="block">Activity</label>
        <select name="activities_id" class="form-select">
            @foreach ($activities as $activity)
                <option value="{{ $activity->id }}">{{ $activity->nama_kegiatan }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="block">Kode Sertifikat</label>
        <input type="text" name="kode_sertifikat" class="form-input">
    </div>
    <div class="mb-3">
        <label class="block">File Sertifikat</label>
        <input type="file" name="file_sertifikat" class="form-input">
    </div>
    <div class="mb-3">
        <label class="block">Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit" class="form-input">
    </div>
    <div class="mb-3">
        <label class="block">Status</label>
        <select name="status" class="form-select">
            <option value="Diterbitkan">Diterbitkan</option>
            <option value="Belum Diterbitkan">Belum Diterbitkan</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>
<script>
    const tipeSelect = document.getElementById('tipe-select');
    const courseDiv = document.getElementById('course-select');
    const activityDiv = document.getElementById('activity-select');
    tipeSelect.addEventListener('change', function () {
        if (this.value === 'course') {
            courseDiv.classList.remove('hidden');
            activityDiv.classList.add('hidden');
        } else {
            courseDiv.classList.add('hidden');
            activityDiv.classList.remove('hidden');
        }
    });
</script>
@endsection