@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>Buat Project Baru</h1>
    <form action="{{ route('projects.peserta.store') }}" method="POST" id="projectForm">
        @csrf
        <div class="mb-3">
            <label for="course_id" class="form-label">Kursus</label>
            <select name="course_id" id="course_id" class="form-control">
                <option value="">Pilih Kursus</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->nama_kelas }}</option>
                @endforeach
            </select>
            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Judul Project</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="html_code" class="form-label">HTML</label>
            <textarea name="html_code" id="html_code" class="form-control" oninput="updatePreview()">{{ old('html_code') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="css_code" class="form-label">CSS</label>
            <textarea name="css_code" id="css_code" class="form-control" oninput="updatePreview()">{{ old('css_code') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="js_code" class="form-label">JavaScript</label>
            <textarea name="js_code" id="js_code" class="form-control" oninput="updatePreview()">{{ old('js_code') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Buat Project</button>
    </form>

    <h2>Live Preview</h2>
    <iframe id="preview" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>
</div>

<script>
    function updatePreview() {
        var iframe = document.getElementById('preview');
        var htmlCode = document.getElementById('html_code').value;
        var cssCode = document.getElementById('css_code').value;
        var jsCode = document.getElementById('js_code').value;

        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(`
            <html>
                <head>
                    <style>${cssCode}</style>
                </head>
                <body>
                    ${htmlCode}
                    <script>${jsCode}<\/script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }
</script>
@endsection
