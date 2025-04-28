@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>{{ $project->title }}</h1>
    <h5>Kursus: {{ $project->course->nama_kursus }}</h5>

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

    <h2>Live Preview</h2>
    <iframe id="preview" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>

    <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning">Edit Project</a>
    <a href="{{ route('projects.peserta.index') }}" class="btn btn-secondary">Kembali ke Daftar Project</a>
</div>

<script>
    // Function to render the preview in the iframe after page load
    window.onload = function() {
        var iframe = document.getElementById('preview');
        var htmlCode = @json($project->html_code); // Mengambil HTML
        var cssCode = @json($project->css_code); // Mengambil CSS
        var jsCode = @json($project->js_code);   // Mengambil JS

        // Memastikan iframe ada dan bisa diubah
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();

        // Menulis konten HTML, CSS, dan JS ke dalam iframe
        iframeDoc.write(`
            <html>
                <head>
                    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css">
                    <style>{{ cssCode }}</style>
                </head>
                <body>
                    {{ htmlCode }}
                    
                    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
                    <script>{{ jsCode }}</script>
                </body>
            </html>
        `);

        iframeDoc.close();
    }
</script>
@endsection
