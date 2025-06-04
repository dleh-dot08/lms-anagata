@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>

    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'overview'])

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Deskripsi Kursus</h5>
            <p>{{ $course->deskripsi }}</p>

            <hr>

            <h6>Jumlah Pertemuan</h6>
            <p>{{ $course->meetings->count() }} pertemuan</p>

            <h6>Jumlah Materi</h6>
            <p>{{ $course->lessons->count() }} materi</p>

            <h6>Mentor Utama</h6>
            <p>{{ $course->mentor->name ?? 'Belum ditentukan' }}</p>

            <h6>Mentor Cadangan 1</h6>
            <p>{{ $course->mentor2->name ?? 'Belum ditentukan' }}</p>

            <h6>Mentor Cadangan 2</h6>
            <p>{{ $course->mentor3->name ?? 'Belum ditentukan' }}</p>

            <h6>Jenjang</h6>
            <p>{{ $course->jenjang->nama_jenjang ?? 'Belum ditentukan' }}</p>

            <h6>Kelas</h6>
            <p>{{ $course->kelas->nama ?? 'Belum ditentukan' }}</p>
        </div>
    </div>
</div>
@endsection