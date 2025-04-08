@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Detail Kursus</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>{{ $courses->nama_kelas }}</h3>
            <p><strong>Mentor:</strong> {{ $courses->mentor->name ?? 'Tidak Ada' }}</p>
            <p><strong>Kategori:</strong> {{ $courses->kategori_id->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Jenjang:</strong> {{ $courses->jenjang_id->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $courses->level }}</p>
            <p><strong>Status:</strong> {{ $courses->status }}</p>
            <p><strong>Deskripsi:</strong> {{ $courses->deskripsi }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $courses->waktu_mulai }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $courses->waktu_akhir }}</p>
            <p><strong>Harga:</strong> {{ $courses->harga ?? 'Gratis' }}</p>
            <p><strong>Jumlah Peserta:</strong> {{ $courses->jumlah_peserta }}</p>
        </div>
    </div>

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
