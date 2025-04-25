@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h3 class="fw-bold py-3 mb-1 mt-2">Detail Sertifikat</h3>
    <div class="card shadow-sm p-4">

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

        <div class="mb-3">
            <a href="{{ route('certificates.indexPeserta') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Daftar Sertifikat
            </a>
        </div>

        <div class="mb-3">
            <h5><strong>Kode Sertifikat:</strong> {{ $certificate->kode_sertifikat }}</h5>
            <h5><strong>Status:</strong> {{ $certificate->status }}</h5>
            <h5><strong>Tanggal Terbit:</strong> {{ $certificate->tanggal_terbit->format('d-m-Y') }}</h5>

            @if($certificate->course_id)
                <h5><strong>Kursus:</strong> {{ $certificate->course->nama_kelas }}</h5>
            @endif

            @if($certificate->activities_id)
                <h5><strong>Aktivitas:</strong> {{ $certificate->activity->nama_aktivitas }}</h5>
            @endif

            <h5><strong>File Sertifikat:</strong> <a href="{{ asset('storage/' . $certificate->file_sertifikat) }}" target="_blank">Lihat Sertifikat</a></h5>
        </div>
    </div>
</div>
@endsection