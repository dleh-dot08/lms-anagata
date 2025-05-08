@extends('layouts.peserta.template')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Project</h2>
    
    <form action="{{ route('projects.peserta.update', $project->id) }}" method="POST" id="projectForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="course_id" class="form-label fw-semibold">Kursus</label>
            <select class="form-select" disabled>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $project->course_id ? 'selected' : '' }}>{{ $course->nama_kelas }}</option>
                @endforeach
            </select>
            <input type="hidden" name="course_id" value="{{ $project->course_id }}">
            @error('course_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label fw-semibold">Judul Project</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: My Portfolio Website" value="{{ old('title', $project->title) }}">
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="row">
            <!-- HTML -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-primary">
                    <div class="card-header bg-primary text-white fw-semibold">
                        <i class="bi bi-filetype-html"></i> HTML
                    </div>
                    <div class="card-body bg-light p-2">
                        <textarea name="html_code" id="html_code" class="form-control code-editor" rows="12" oninput="updatePreview()">{{ old('html_code', $project->html_code) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- CSS -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-success">
                    <div class="card-header bg-success text-white fw-semibold">
                        <i class="bi bi-filetype-css"></i> CSS
                    </div>
                    <div class="card-body bg-light p-2">
                        <textarea name="css_code" id="css_code" class="form-control code-editor" rows="12" oninput="updatePreview()">{{ old('css_code', $project->css_code) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- JavaScript -->
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-warning">
                    <div class="card-header bg-warning text-dark fw-semibold">
                        <i class="bi bi-filetype-js"></i> JavaScript
                    </div>
                    <div class="card-body bg-light p-2">
                        <textarea name="js_code" id="js_code" class="form-control code-editor" rows="12" oninput="updatePreview()">{{ old('js_code', $project->js_code) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <h4 class="mt-4"><i class="bi bi-eye-fill"></i> Live Preview</h4>
        <div class="card shadow-sm mb-4">
            <div class="card-body p-0">
                <iframe id="preview" class="w-100" style="height: 500px; border: 1px solid #ccc;"></iframe>
            </div>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-lg btn-warning">
            <i class="bi bi-save-fill"></i> Update Project
        </button>
    </form>
</div>

<style>
    .code-editor {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
    }
</style>

<script>
    function updatePreview() {
        const htmlCode = document.getElementById('html_code').value;
        const cssCode = document.getElementById('css_code').value;
        const jsCode = document.getElementById('js_code').value;

        const iframe = document.getElementById('preview');
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        iframeDoc.open();
        iframeDoc.write(`
            <html>
                <head><style>${cssCode}</style></head>
                <body>
                    ${htmlCode}
                    <script>
                        try { ${jsCode} } catch(e) { console.error(e); }
                    <\/script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }

    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endsection
