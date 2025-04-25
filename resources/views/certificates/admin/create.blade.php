@extends('layouts.admin.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold py-3 mb-1 mt-2">Tambah Sertifikat</h3>

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

        <!-- Form Tambah Sertifikat -->
        <form method="POST" action="{{ route('certificates.store') }}">
            @csrf
            <div class="mb-3">
                <label for="user_id" class="form-label">Nama Peserta</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Pilih Peserta</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipe Sertifikat</label>
                <select id="type" name="type" class="form-control" onchange="toggleCourseOrActivity()">
                    <option value="course">Course</option>
                    <option value="activity">Activity</option>
                </select>
            </div>

            <div class="mb-3" id="course-container">
                <label for="course_id" class="form-label">Pilih Kursus</label>
                <select name="course_id" id="course_id" class="form-control">
                    <option value="">Pilih Kursus</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="activity-container" style="display: none;">
                <label for="activities_id" class="form-label">Pilih Aktivitas</label>
                <select name="activities_id" id="activities_id" class="form-control">
                    <option value="">Pilih Aktivitas</option>
                    @foreach($activities as $activity)
                        <option value="{{ $activity->id }}">{{ $activity->nama_kegiatan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="kode_sertifikat" class="form-label">Kode Sertifikat</label>
                <input type="text" name="kode_sertifikat" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="file_sertifikat" class="form-label">File Sertifikat</label>
                <input type="file" name="file_sertifikat" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                <input type="date" name="tanggal_terbit" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="Diterbitkan">Diterbitkan</option>
                    <option value="Belum Diterbitkan">Belum Diterbitkan</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Sertifikat</button>
        </form>
    </div>
</div>

<script>
    // Fungsi untuk menampilkan atau menyembunyikan pilihan berdasarkan tipe sertifikat
    function toggleCourseOrActivity() {
        const type = document.getElementById('type').value;
        const courseContainer = document.getElementById('course-container');
        const activityContainer = document.getElementById('activity-container');

        if (type === 'course') {
            courseContainer.style.display = 'block';
            activityContainer.style.display = 'none';
        } else {
            courseContainer.style.display = 'none';
            activityContainer.style.display = 'block';
        }
    }
</script>

@endsection
