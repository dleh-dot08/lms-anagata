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

    <!-- Preview Section -->
    <h2>Live Preview</h2>
    <iframe id="preview" style="width: 100%; height: 400px; border: 1px solid #ddd;"></iframe>

    <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning">Edit Project</a>
    <a href="{{ route('projects.peserta.index') }}" class="btn btn-secondary">Kembali ke Daftar Project</a>
</div>

<script>
    // Function to render the preview in the iframe
    window.onload = function() {
        var iframe = document.getElementById('preview');
        var htmlCode = @json($project->html_code);
        var cssCode = @json($project->css_code);
        var jsCode = @json($project->js_code);

        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        
        iframeDoc.open();
        iframeDoc.write(`
            <html>
                <head>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">
                    <style>${cssCode}</style>
                </head>
                <body>
                    ${htmlCode}
                    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
                    <script>${jsCode}<\/script>
                </body>
            </html>
        `);
        iframeDoc.close();
    }
</script>
@endsection
