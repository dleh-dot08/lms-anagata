@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Detail Kursus</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>{{ $course->nama_kelas }}</h3>
            <p><strong>Mentor:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
            <p><strong>Kategori:</strong> {{ $course->kategori->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Jenjang:</strong> {{ $course->jenjang->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $course->level }}</p>
            <p><strong>Status:</strong> {{ $course->status }}</p>
            <p><strong>Deskripsi:</strong> {{ $course->deskripsi }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $course->waktu_mulai }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $course->waktu_akhir }}</p>
            <p><strong>Harga:</strong> {{ $course->harga ?? 'Gratis' }}</p>
            <p><strong>Jumlah Peserta:</strong> {{ $course->jumlah_peserta }}</p>
        </div>
    </div>

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
