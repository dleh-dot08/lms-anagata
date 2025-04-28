@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>{{ $project->title }}</h1>
    <h5>Kursus: {{ $project->course->nama_kelas }}</h5>

    <div class="mb-3">
        <h4>Preview Project</h4>
        <iframe id="preview" style="width: 100%; height: 500px; border: 1px solid #ddd;"></iframe>
    </div>

    <div class="mb-3">
        <h4>Kode HTML</h4>
        <pre>{{ $project->html_code }}</pre>
    </div>

    <div class="mb-3">
        <h4>Kode CSS</h4>
        <pre>{{ $project->css_code }}</pre>
    </div>

    <div class="mb-3">
        <h4>Kode JavaScript</h4>
        <pre>{{ $project->js_code }}</pre>
    </div>

    <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning">Edit Project</a>
    <a href="{{ route('projects.peserta.index') }}" class="btn btn-secondary">Kembali ke Daftar Project</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var iframe = document.getElementById('preview');
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        var htmlCode = `{!! addslashes($project->html_code) !!}`;
        var cssCode = `{!! addslashes($project->css_code) !!}`;
        var jsCode = `{!! addslashes($project->js_code) !!}`;

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
    });
</script>
@endsection
