<!-- resources/views/projects/karyawan/show.blade.php -->
@extends('layouts.karyawan.template')

@section('content')
<div class="container">
    <h1>{{ $project->title }}</h1>
    <h5>Peserta: {{ $project->user->name }}</h5>
    <h5>Kursus: {{ $project->course->name }}</h5>
    
    <div class="mb-3">
        <h4>HTML</h4>
        <pre>{{ $project->html_code }}</pre>
    </div>

    <div class="mb-3">
        <h4>CSS</h4>
        <pre>{{ $project->css_code }}</pre>
    </div>

    <div class="mb-3">
        <h4>JavaScript</h4>
        <pre>{{ $project->js_code }}</pre>
    </div>

    <a href="{{ route('projects.index') }}" class="btn btn-secondary">Kembali ke Daftar Project</a>
</div>
@endsection
