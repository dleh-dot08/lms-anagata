@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-transparent" style="background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="bx bx-calendar-event fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-white">Daftar Pertemuan</h3>
                                <p class="mb-0 fs-5 opacity-90">{{ $course->nama_kelas }}</p>
                                <small class="opacity-75">
                                    <i class="bx bx-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($course->waktu_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($course->waktu_akhir)->format('d M Y') }}
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.notes.index') }}" class="btn btn-light btn-sm">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light shadow-sm">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-user text-primary me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $course->mentor->name ?? 'Belum ditentukan' }}</h6>
                                    <small class="text-muted">Mentor</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-book text-success me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $course->kategori->nama ?? 'Tidak ada kategori' }}</h6>
                                    <small class="text-muted">Kategori</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-group text-info me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $course->participants->count() }} Peserta</h6>
                                    <small class="text-muted">Terdaftar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($course->meetings->isEmpty())
        <div class="alert alert-warning">
            <div class="d-flex align-items-center">
                <i class="bx bx-info-circle me-2 fs-5"></i>
                <div>Belum ada pertemuan yang tersedia untuk kelas ini.</div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Pertemuan</th>
                                        <th>Tanggal</th>
                                        <th>Judul</th>
                                        <th>Status Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->meetings as $meeting)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">Pertemuan {{ $meeting->pertemuan }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('d M Y') }}</td>
                                        <td>{{ $meeting->judul ?? 'Tidak ada judul' }}</td>
                                        <td>
                                            @if($meeting->note)
                                                <span class="badge bg-success">Tersedia</span>
                                            @else
                                                <span class="badge bg-danger">Belum Dibuat</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($meeting->note)
                                                <a href="{{ route('admin.notes.show', $meeting->id) }}" class="btn btn-info btn-sm">
                                                    <i class="bx bx-show me-1"></i> Lihat Catatan
                                                </a>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="bx bx-x-circle me-1"></i> Belum Ada Catatan
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .table th, .table td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
    }
</style>
@endsection