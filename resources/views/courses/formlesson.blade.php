@extends('layouts.admin.template')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ isset($lesson) ? route('courses.updateLesson', [$course->id, $lesson->id]) : route('courses.storeLesson', $course->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($lesson))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="judul" class="form-label">Judul Materi</label>
                <input type="text" class="form-control" name="judul" value="{{ old('judul', $lesson->judul ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label for="konten" class="form-label">Konten</label>
                <textarea class="form-control" name="konten">{{ old('konten', $lesson->konten ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="pertemuan_ke" class="form-label">Pertemuan Ke</label>
                <input type="number" class="form-control" name="pertemuan_ke" value="{{ old('pertemuan_ke', $lesson->pertemuan_ke ?? '') }}" required>
            </div>

            @for ($i = 1; $i <= 3; $i++)
                <div class="mb-3">
                    <label for="video_url{{ $i }}" class="form-label">Video URL {{ $i }}</label>
                    <input type="text" class="form-control" name="video_url{{ $i }}" value="{{ old("video_url$i", $lesson->{"video_url$i"} ?? '') }}">
                </div>
            @endfor

            @for ($i = 1; $i <= 5; $i++)
                <div class="mb-3">
                    <label for="file_materi{{ $i }}" class="form-label">File Materi {{ $i }}</label>
                    <input type="text" class="form-control" name="file_materi{{ $i }}" value="{{ old("file_materi$i", $lesson->{"file_materi$i"} ?? '') }}">
                </div>
            @endfor

            <button type="submit" class="btn btn-success">{{ isset($lesson) ? 'Update' : 'Simpan' }}</button>
        </form>
    </div>
</div>
@endsection
