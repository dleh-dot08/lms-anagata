@extends('layouts.peserta.template')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow rounded-4">
                <div class="card-body">
                    <h2 class="mb-4 text-center">Buat Project Baru</h2>
                    <h5 class="text-muted text-center mb-4">Kursus: <strong>{{ $course->nama_kelas }}</strong></h5>

                    <form action="{{ route('projects.peserta.storeCourse', $course->id) }}" method="POST" id="projectForm">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">Judul Project</label>
                            <input type="text" name="title" id="title" class="form-control rounded-3" value="{{ old('title') }}" placeholder="Contoh: Website Portfolio">
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label for="html_code" class="form-label fw-semibold">HTML</label>
                                <textarea name="html_code" id="html_code" rows="10" class="form-control font-monospace rounded-3" placeholder="Tulis HTML Anda disini..." oninput="updatePreview()">{{ old('html_code') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="css_code" class="form-label fw-semibold">CSS</label>
                                <textarea name="css_code" id="css_code" rows="10" class="form-control font-monospace rounded-3" placeholder="Tulis CSS Anda disini..." oninput="updatePreview()">{{ old('css_code') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label for="js_code" class="form-label fw-semibold">JavaScript</label>
                                <textarea name="js_code" id="js_code" rows="10" class="form-control font-monospace rounded-3" placeholder="Tulis JavaScript Anda disini..." oninput="updatePreview()">{{ old('js_code') }}</textarea>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow">💾 Simpan Project</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow rounded-4 mt-5">
                <div class="card-body">
                    <h4 class="text-center mb-3">🔍 Live Preview</h4>
                    <iframe id="preview" class="w-100 border rounded-3 shadow-sm" style="height: 500px;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <head>
                    <style>${cssCode}</style>
                </head>
                <body>
                    ${htmlCode}
                    <script>
                        try {
                            ${jsCode}
                        } catch (error) {
                            console.error('Error in JS:', error);
                        }
                    <\/script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }

    // Run once on load to show any default code
    window.onload = updatePreview;
</script>
@endsection
