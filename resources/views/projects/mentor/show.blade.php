<!-- resources/views/projects/mentor/show.blade.php -->
@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary mb-1">{{ $project->title }}</h2>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-info text-dark">Peserta: {{ $project->user->name }}</span>
                <span class="badge bg-success text-dark">Kelas: {{ $project->course->nama_kelas ?? '-' }}</span>
            </div>
            <p class="text-muted">{{ $project->description ?? 'Tidak ada deskripsi tersedia.' }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('mentor.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Project
            </a>
        </div>
    </div>

    <!-- Preview dan Code -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header p-0 bg-light">
            <ul class="nav nav-tabs" id="mentorTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-pane" type="button" role="tab" aria-controls="preview-pane" aria-selected="true">
                        <i class="fas fa-eye me-1"></i> Preview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="code-tab" data-bs-toggle="tab" data-bs-target="#code-pane" type="button" role="tab" aria-controls="code-pane" aria-selected="false">
                        <i class="fas fa-code me-1"></i> Source Code
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content">
                <!-- Preview -->
                <div class="tab-pane fade show active" id="preview-pane" role="tabpanel" aria-labelledby="preview-tab">
                    <iframe id="preview" style="width: 100%; height: 600px; border: none;"></iframe>
                </div>

                <!-- Code -->
                <div class="tab-pane fade" id="code-pane" role="tabpanel" aria-labelledby="code-tab">
                    <div class="p-3 bg-dark text-light">
                        <ul class="nav nav-pills mb-3" id="codeSubTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="html-tab" data-bs-toggle="pill" data-bs-target="#html-code" type="button" role="tab" aria-controls="html-code" aria-selected="true">
                                    HTML
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="css-tab" data-bs-toggle="pill" data-bs-target="#css-code" type="button" role="tab" aria-controls="css-code" aria-selected="false">
                                    CSS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="js-tab" data-bs-toggle="pill" data-bs-target="#js-code" type="button" role="tab" aria-controls="js-code" aria-selected="false">
                                    JavaScript
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="html-code" role="tabpanel" aria-labelledby="html-tab">
                                <pre class="code-editor"><code class="language-html">{{ $project->html_code }}</code></pre>
                            </div>
                            <div class="tab-pane fade" id="css-code" role="tabpanel" aria-labelledby="css-tab">
                                <pre class="code-editor"><code class="language-css">{{ $project->css_code }}</code></pre>
                            </div>
                            <div class="tab-pane fade" id="js-code" role="tabpanel" aria-labelledby="js-tab">
                                <pre class="code-editor"><code class="language-javascript">{{ $project->js_code }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Tambahan -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p><strong>Dibuat:</strong> {{ $project->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Update Terakhir:</strong> {{ $project->updated_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
</div>

<style>
    .code-editor {
        background-color: #1e1e1e;
        padding: 1rem;
        border-radius: 0.25rem;
        overflow-x: auto;
        white-space: pre-wrap;
        font-family: 'Courier New', monospace;
        max-height: 500px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const iframe = document.getElementById('preview');
        const html = {!! json_encode($project->html_code) !!};
        const css = {!! json_encode($project->css_code) !!};
        const js = {!! json_encode($project->js_code) !!};

        function renderPreview() {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(`
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>${css}</style>
                </head>
                <body>
                    ${html}
                    <script>${js}<\/script>
                </body>
                </html>
            `);
            doc.close();
        }

        renderPreview();
    });
</script>
@endsection
