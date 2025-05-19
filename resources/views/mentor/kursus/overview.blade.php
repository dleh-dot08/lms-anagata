@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-0">{{ $course->nama_kelas }}</h3>

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

            <h6>Mentor</h6>
            <p>{{ $course->mentor->name ?? 'Belum ditentukan' }}</p>
        </div>
    </div>
</div>
@endsection