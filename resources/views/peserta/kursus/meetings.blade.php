@extends('layouts.peserta.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>
    @include('peserta.kursus.partials.nav-tabs', ['activeTab' => 'meetings'])

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-book mr-2"></i>Materi Pembelajaran</h5>
        </div>
        <div class="card-body">
            @if($course->lessons && count($course->lessons))
                <div class="list-group">
                    @foreach ($course->lessons as $lesson)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge badge-primary mr-2 text-dark">
                                    {{ $lesson->meeting->pertemuan ?? '-' }}
                                </span>
                                <span>{{ $lesson->meeting->judul ?? $lesson->judul }}</span>
                            </div>
                            <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye mr-1"></i> Lihat Materi
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>Belum ada materi tersedia untuk kursus ini.
                </div>
            @endif                    
        </div>
    </div>
</div>
@endsection