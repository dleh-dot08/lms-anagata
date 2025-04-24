@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <div class="container mt-4">
        <h2 class="mb-4">Menu Absensi</h2>
        <div class="row">
            <!-- Kartu Absensi Kursus -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Absensi Kursus</h5>
                        <p class="card-text">Lakukan absensi untuk kelas yang Anda ikuti hari ini.</p>
                        <a href="{{ route('attendances.courses') }}" class="btn btn-primary mt-auto">Masuk</a>
                    </div>
                </div>
            </div>
    
            <!-- Kartu Absensi Kegiatan -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Absensi Kegiatan</h5>
                        <p class="card-text">Lakukan absensi untuk kegiatan yang Anda ikuti hari ini.</p>
                        <a href="{{ route('attendances.activities') }}" class="btn btn-success mt-auto">Masuk</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
