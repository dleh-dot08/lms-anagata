@extends('layouts.admin.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold py-3 mb-1 mt-2">Detail Sertifikat</h3>

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
            <a href="{{ route('certificates.indexadmin') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Daftar Sertifikat
            </a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">Nama Peserta:</h5>
                <p>{{ $certificate->user->name }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold">Kode Sertifikat:</h5>
                <p>{{ $certificate->kode_sertifikat }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">Tipe Sertifikat:</h5>
                <p>{{ ucfirst($certificate->type) }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold">Status:</h5>
                <p>
                    @if ($certificate->status == 'Diterbitkan')
                        <span class="badge bg-success">Diterbitkan</span>
                    @else
                        <span class="badge bg-warning">Belum Diterbitkan</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row">
            @if ($certificate->type == 'course')
                <div class="col-md-6">
                    <h5 class="fw-bold">Kursus:</h5>
                    <p>{{ $certificate->course->nama_kelas ?? 'N/A' }}</p>
                </div>
            @elseif ($certificate->type == 'activity')
                <div class="col-md-6">
                    <h5 class="fw-bold">Aktivitas:</h5>
                    <p>{{ $certificate->activity->nama_aktivitas ?? 'N/A' }}</p>
                </div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">Tanggal Terbit:</h5>
                <p>{{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d-m-Y') }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold">File Sertifikat:</h5>
                <a href="{{ asset('storage/' . $certificate->file_sertifikat) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Sertifikat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
