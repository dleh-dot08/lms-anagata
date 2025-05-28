@extends('layouts.sekolah.template')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">
        <span class="text-dark">Daftar Silabus Kursus</span>
    </h3>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($courses->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-2"></i>Belum ada kursus yang tersedia untuk sekolah Anda.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kursus</th>
                                <th>Kategori</th>
                                <th>Jenjang</th>
                                <th>Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $index => $course)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $course->nama_kelas }}</td>
                                    <td>{{ $course->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ $course->jenjang->nama_jenjang ?? '-' }}</td>
                                    <td>
                                        @if($course->waktu_mulai && $course->waktu_akhir)
                                            {{ \Carbon\Carbon::parse($course->waktu_mulai)->translatedFormat('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($course->waktu_akhir)->translatedFormat('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($course->silabus_pdf)
                                                <a href="{{ route('sekolah.silabus.show', $course->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i>Lihat Silabus
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i>Silabus Belum Tersedia
                                                </button>
                                            @endif
                                            
                                            @if($course->rpp_drive_link)
                                                <a href="{{ $course->rpp_drive_link }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-file-earmark-text me-1"></i>Lihat RPP
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-file-earmark-text me-1"></i>RPP Belum Tersedia
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection