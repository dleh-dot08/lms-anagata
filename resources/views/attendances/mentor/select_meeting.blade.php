@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Absen Siswa - {{ $course->nama_kelas }}</h4>
        <a href="{{ route('mentor.attendances.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Pilih Pertemuan</h5>
            
            <div class="list-group">
                @foreach($meetings as $meeting)
                    <a href="{{ route('mentor.attendances.create', ['course' => $course->id, 'meeting' => $meeting->id]) }}" 
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Pertemuan {{ $meeting->pertemuan }}</h6>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->format('d F Y') }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $meeting->absensi_diisi ? 'success' : 'warning' }} rounded-pill">
                            {{ $meeting->absensi_diisi ? 'Sudah Diabsen' : 'Belum Diabsen' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection