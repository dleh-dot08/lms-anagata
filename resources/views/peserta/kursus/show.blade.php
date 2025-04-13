@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h3>{{ $course->nama_kelas }}</h3>

    @if ($course->status == 'aktif' && \Carbon\Carbon::now()->between($course->waktu_mulai, $course->waktu_akhir))
        <div class="alert alert-success">
            Kursus ini sedang aktif dan dapat diikuti.
        </div>

        <h5>Deskripsi Kursus</h5>
        <p>{{ $course->deskripsi }}</p>

        <h5>Materi Kursus</h5>
        <ul>
            @foreach ($course->lessons as $lesson)
                <li>
                    <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}">
                        {{ $lesson->judul }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-danger">
            Kursus ini sudah tidak aktif atau telah berakhir.
        </div>
    @endif

    <a href="{{ route('courses.indexpeserta') }}" class="btn btn-secondary">Kembali ke Daftar Kursus</a>
</div>
@endsection
