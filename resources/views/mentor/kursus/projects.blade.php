@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>
    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'project'])
    <h5><strong>Project Saya</strong></h5>
    <a href="{{ route('projects.peserta.createCourse', ['course' => $course->id]) }}" class="btn btn-primary mb-3">
        Buat Project
    </a>

    @php
        $myProjects = $course->projects->where('user_id', auth()->id());
    @endphp

    @if($myProjects->count())
        <div class="row">
            @foreach($myProjects as $project)
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">{{ $project->title }}</h6>
                            <p class="card-text">Peserta: {{ $project->user->name ?? 'Tidak diketahui' }}</p>
                            <a href="{{ route('projects.peserta.show', $project->id) }}" class="btn btn-sm btn-primary">Lihat Project</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">Kamu belum membuat project untuk kursus ini.</p>
    @endif
</div>
@endsection