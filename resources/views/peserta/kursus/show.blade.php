@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4 mt-4">
        <div class="card-header bg-primary mb-4">
            <h4 class="mb-0 text-white">{{ $course->nama_kelas }}</h4>
        </div>
        <div class="card-body">
            {{-- Cek status aktif --}}
            @if($course->status === 'Aktif' && now()->between($course->waktu_mulai, $course->waktu_akhir))
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Kode Unik:</strong> {{ $course->kode_unik  ?? '-' }}</p>
                        <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori ?? '-' }}</p>
                        <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Level:</strong> {{ ucfirst($course->level) }}</p>
                        <p><strong>Waktu Mulai:</strong> {{ \Carbon\Carbon::parse($course->waktu_mulai)->translatedFormat('d M Y') }}</p>
                        <p><strong>Waktu Akhir:</strong> {{ \Carbon\Carbon::parse($course->waktu_akhir)->translatedFormat('d M Y') }}</p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h5><strong>Deskripsi Kursus </strong></h5>
                    <p>{!! nl2br(e($course->deskripsi)) !!}</p>
                </div>

                <hr>

                <div class="mb-3">
                    <h5><strong>Materi Pembelajaran</strong></h5>
                    @if($course->lessons && count($course->lessons))
                        <ul class="list-group">
                            @foreach ($course->lessons as $lesson)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Pertemuan {{ $lesson->pertemuan_ke }}: {{ $lesson->judul }}
                                    <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-outline-primary">
                                        Lihat Materi
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Belum ada materi tersedia.</p>
                    @endif
                </div>

            @else
                <div class="alert alert-warning text-center">
                    <h5>Kursus Tidak Aktif</h5>
                    <p>Kursus ini sudah tidak aktif atau telah berakhir. Silakan hubungi admin jika kamu memerlukan akses ulang.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
