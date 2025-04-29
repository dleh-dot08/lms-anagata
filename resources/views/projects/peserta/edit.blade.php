@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>Edit Project</h1>
    <form action="{{ route('projects.peserta.update', $project->id) }}" method="POST" id="projectForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="course_id" class="form-label">Kursus</label>
            <select name="course_id_display" id="course_id" class="form-control" disabled>
                <option value="">Pilih Kursus</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $project->course_id ? 'selected' : '' }}>{{ $course->nama_kelas }}</option>
                @endforeach
            </select>
            <input type="hidden" name="course_id" value="{{ $project->course_id }}">
            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Judul Project</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $project->title) }}">
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">HTML</div>
                    <div class="card-body">
                        <textarea name="html_code" id="html_code" class="form-control" rows="10" oninput="updatePreview()">{{ old('html_code', $project->html_code) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">CSS</div>
                    <div class="card-body">
                        <textarea name="css_code" id="css_code" class="form-control" rows="10" oninput="updatePreview()">{{ old('css_code', $project->css_code) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">JavaScript</div>
                    <div class="card-body">
                        <textarea name="js_code" id="js_code" class="form-control" rows="10" oninput="updatePreview()">{{ old('js_code', $project->js_code) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <h4>Live Preview</h4>
        <iframe id="preview" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>

        <button type="submit" class="btn btn-warning mt-3">Update Project</button>
    </form>
</div>

<script>
    function updatePreview() {
        var htmlCode = document.getElementById('html_code').value;
        var cssCode = document.getElementById('css_code').value;
        var jsCode = document.getElementById('js_code').value;

        var iframe = document.getElementById('preview');
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        iframeDoc.open();
        iframeDoc.write(`
            <html>
                <head>
                    <style>${cssCode}</style>
                </head>
                <body>
                    ${htmlCode}
                    <script>
                        try { ${jsCode} } catch (e) { console.error(e); }
                    <\/script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection
