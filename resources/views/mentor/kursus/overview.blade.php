@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-0">{{ $course->nama_kelas }} — Overview</h3>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('mentor.kursus.show', $course->id) }}">Meetings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Modul</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Assignment</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Project</a>
        </li>
    </ul>

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
