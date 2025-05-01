<!-- resources/views/projects/admin/show.blade.php -->
@extends('layouts.admin.template')

@section('content')
<div class="container py-4">
    <h5>Peserta: {{ $project->user->name }}</h5>
    <h2 class="mb-3">{{ $project->title }}</h2>
    <h2 class="text-muted mb-4">Kursus: {{ $project->course->nama_kelas ?? '-' }}</h5>

    <div class="row mb-4">
        <!-- Card HTML -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">HTML</h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0" style="white-space: pre-wrap;">{{ $project->html_code }}</pre>
                </div>
            </div>
        </div>

        <!-- Card CSS -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">CSS</h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0" style="white-space: pre-wrap;">{{ $project->css_code }}</pre>
                </div>
            </div>
        </div>

        <!-- Card JavaScript -->
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">JavaScript</h6>
                </div>
                <div class="card-body">
                    <pre class="mb-0" style="white-space: pre-wrap;">{{ $project->js_code }}</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">Preview Project</h6>
        </div>
        <div class="card-body p-0">
            <iframe id="preview" style="width: 100%; height: 600px; border: none;"></iframe>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var iframe = document.getElementById('preview');
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        var htmlCode = {!! json_encode($project->html_code) !!};
        var cssCode = {!! json_encode($project->css_code) !!};
        var jsCode = {!! json_encode($project->js_code) !!};

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
