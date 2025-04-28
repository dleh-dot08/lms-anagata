@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>Edit Project</h1>
    <form action="{{ route('projects.peserta.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="course_id" class="form-label">Kursus</label>
            <select name="course_id" id="course_id" class="form-control">
                <option value="">Pilih Kursus</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $project->course_id ? 'selected' : '' }}>{{ $course->name }}</option>
                @endforeach
            </select>
            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Judul Project</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $project->title) }}">
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="html_code" class="form-label">HTML</label>
            <textarea name="html_code" id="html_code" class="form-control">{{ old('html_code', $project->html_code) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="css_code" class="form-label">CSS</label>
            <textarea name="css_code" id="css_code" class="form-control">{{ old('css_code', $project->css_code) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="js_code" class="form-label">JavaScript</label>
            <textarea name="js_code" id="js_code" class="form-control">{{ old('js_code', $project->js_code) }}</textarea>
        </div>

        <button type="submit" class="btn btn-warning">Update Project</button>
    </form>
</div>
@endsection
