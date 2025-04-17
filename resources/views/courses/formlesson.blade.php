@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ isset($lesson) ? 'Edit Materi' : 'Tambah Materi' }}</h5>
            <form action="{{ isset($lesson) ? route('courses.updateLesson', [$course->id, $lesson->id]) : route('courses.storeLesson', $course->id) }}" method="POST">
                @csrf
                @if(isset($lesson))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Materi</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $lesson->judul ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="pertemuan_ke" class="form-label">Pertemuan Ke</label>
                    <input type="number" name="pertemuan_ke" class="form-control" value="{{ old('pertemuan_ke', $lesson->pertemuan_ke ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label for="konten" class="form-label">Konten</label>
                    <textarea name="konten" class="form-control" id="editor">{{ old('konten', $lesson->konten ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link Video (YouTube/Google Drive)</label>
                    <input type="text" name="video_url1" class="form-control mb-2" placeholder="Video URL 1" value="{{ old('video_url1', $lesson->video_url1 ?? '') }}">
                    <input type="text" name="video_url2" class="form-control mb-2" placeholder="Video URL 2" value="{{ old('video_url2', $lesson->video_url2 ?? '') }}">
                    <input type="text" name="video_url3" class="form-control mb-2" placeholder="Video URL 3" value="{{ old('video_url3', $lesson->video_url3 ?? '') }}">
                    <small class="text-muted">Gunakan link yang bisa dibuka publik dan tampilkan dengan iframe di halaman show.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link File Materi (Google Drive)</label>
                    <input type="text" name="file_materi1" class="form-control mb-2" placeholder="File Materi 1" value="{{ old('file_materi1', $lesson->file_materi1 ?? '') }}">
                    <input type="text" name="file_materi2" class="form-control mb-2" placeholder="File Materi 2" value="{{ old('file_materi2', $lesson->file_materi2 ?? '') }}">
                    <input type="text" name="file_materi3" class="form-control mb-2" placeholder="File Materi 3" value="{{ old('file_materi3', $lesson->file_materi3 ?? '') }}">
                    <input type="text" name="file_materi4" class="form-control mb-2" placeholder="File Materi 4" value="{{ old('file_materi4', $lesson->file_materi4 ?? '') }}">
                    <input type="text" name="file_materi5" class="form-control mb-2" placeholder="File Materi 5" value="{{ old('file_materi5', $lesson->file_materi5 ?? '') }}">
                    <small class="text-muted">Gunakan link Google Drive versi <code>/preview</code> agar bisa ditampilkan menggunakan iframe.</small>
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endsection
