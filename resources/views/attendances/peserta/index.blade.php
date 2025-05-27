@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="container mt-4">
        <h2 class="mb-4">Menu Absensi</h2>
        <div class="row">
            <!-- Kartu Absensi Kegiatan -->
            <div class="col-md-12 mb-4">
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
