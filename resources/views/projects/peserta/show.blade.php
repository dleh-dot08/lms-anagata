@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>{{ $project->title }}</h1>
    <h5>Kursus: {{ $project->course->nama_kursus }}</h5>

    <!-- Menampilkan Preview dengan Iframe -->
    <h2>Live Preview</h2>
    <iframe id="preview" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>

    <div class="mb-3">
        <h4>HTML</h4>
        <pre><code>{{ $project->html_code }}</code></pre>
    </div>

    <div class="mb-3">
        <h4>CSS</h4>
        <pre><code>{{ $project->css_code }}</code></pre>
    </div>

    <div class="mb-3">
        <h4>JavaScript</h4>
        <pre><code>{{ $project->js_code }}</code></pre>
    </div>

    <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning">Edit Project</a>
    <a href="{{ route('projects.peserta.index') }}" class="btn btn-secondary">Kembali ke Daftar Project</a>
</div>

<script>
    // Fungsi untuk memperbarui preview di iframe
    function updatePreview() {
        var iframe = document.getElementById('preview');
        var htmlCode = `{{ $project->html_code }}`;
        var cssCode = `{{ $project->css_code }}`;
        var jsCode = `{{ $project->js_code }}`;

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
                        try {
                            ${jsCode}
                        } catch (error) {
                            console.error('Error in JS code:', error);
                        }
                    </script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }

    // Panggil fungsi updatePreview untuk menampilkan preview saat halaman pertama kali dimuat
    updatePreview();
</script>
@endsection
