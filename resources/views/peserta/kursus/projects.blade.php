@extends('layouts.peserta.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>
    @include('peserta.kursus.partials.nav-tabs', ['activeTab' => 'project'])

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-project-diagram mr-2"></i>Project Saya</h5>
            <a href="{{ route('projects.peserta.createCourse', ['course' => $course->id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Buat Project
            </a>
        </div>
        <div class="card-body">
            @php
                $myProjects = $course->projects->where('user_id', auth()->id());
            @endphp

            @if($myProjects->count())
                <div class="row">
                    @foreach($myProjects as $project)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 shadow-sm border-0 hover-card">
                                <div class="card-body">
                                    <h6 class="card-title font-weight-bold">{{ $project->title }}</h6>
                                    <p class="card-text text-muted"><i class="fas fa-user mr-1"></i>{{ $project->user->name ?? 'Tidak diketahui' }}</p>
                                    <a href="{{ route('projects.peserta.show', $project->id) }}" class="btn btn-sm btn-outline-primary mt-2 stretched-link">
                                        <i class="fas fa-eye mr-1"></i> Lihat Project
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light border">
                    <i class="fas fa-info-circle mr-2"></i>Kamu belum membuat project untuk kursus ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.hover-card {
    transition: transform 0.2s ease-in-out;
}

.hover-card:hover {
    transform: translateY(-5px);
}
</style>
@endpush