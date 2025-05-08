@extends('layouts.admin.template')

@section('content')
<div class="container py-4">
    <h5 class="text-muted">Peserta: {{ $project->user->name }}</h5>
    <h2 class="fw-bold mb-3">{{ $project->title }}</h2>
    <h5 class="text-secondary mb-4">Kursus: {{ $project->course->nama_kelas ?? '-' }}</h5>

    <!-- Code Tabs -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-code me-2"></i>Kode Project</span>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="codeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html" type="button" role="tab">HTML</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="css-tab" data-bs-toggle="tab" data-bs-target="#css" type="button" role="tab">CSS</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="js-tab" data-bs-toggle="tab" data-bs-target="#js" type="button" role="tab">JavaScript</button>
                </li>
            </ul>
            <div class="tab-content" id="codeTabContent">
                <div class="tab-pane fade show active" id="html" role="tabpanel">
                    <pre class="bg-light p-3 rounded border" style="white-space: pre-wrap;">{{ $project->html_code }}</pre>
                </div>
                <div class="tab-pane fade" id="css" role="tabpanel">
                    <pre class="bg-light p-3 rounded border" style="white-space: pre-wrap;">{{ $project->css_code }}</pre>
                </div>
                <div class="tab-pane fade" id="js" role="tabpanel">
                    <pre class="bg-light p-3 rounded border" style="white-space: pre-wrap;">{{ $project->js_code }}</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-eye me-2"></i>Preview Project</span>
            <div class="device-selector btn-group">
                <button class="btn btn-sm btn-outline-light active" data-device="desktop">Desktop</button>
                <button class="btn btn-sm btn-outline-light" data-device="tablet">Tablet</button>
                <button class="btn btn-sm btn-outline-light" data-device="mobile">Mobile</button>
                <button class="btn btn-sm btn-outline-light" id="fullscreenBtn"><i class="fas fa-expand"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <iframe id="preview" class="desktop-view" style="width: 100%; height: 600px; border: none;"></iframe>
        </div>
    </div>
</div>

<style>
    .desktop-view { height: 600px; }
    .tablet-view { width: 768px; height: 600px; margin: auto; display: block; }
    .mobile-view { width: 375px; height: 667px; margin: auto; display: block; }

    .fullscreen-preview {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: #fff;
        z-index: 9999;
        display: flex;
        flex-direction: column;
    }

    .fullscreen-controls {
        padding: 10px;
        background: #343a40;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .fullscreen-preview iframe {
        flex: 1;
        border: none;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const iframe = document.getElementById('preview');
        const deviceButtons = document.querySelectorAll('.device-selector .btn');
        const fullscreenBtn = document.getElementById('fullscreenBtn');

        const htmlCode = {!! json_encode($project->html_code) !!};
        const cssCode = {!! json_encode($project->css_code) !!};
        const jsCode = {!! json_encode($project->js_code) !!};

        function renderPreview(targetIframe = iframe) {
            const doc = targetIframe.contentDocument || targetIframe.contentWindow.document;
            doc.open();
            doc.write(`
                <html>
                    <head>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <style>${cssCode}</style>
                    </head>
                    <body>
                        ${htmlCode}
                        <script>${jsCode}<\/script>
                    </body>
                </html>
            `);
            doc.close();
        }

        renderPreview();

        // Switch device view
        deviceButtons.forEach(button => {
            button.addEventListener('click', function () {
                deviceButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const device = this.getAttribute('data-device');
                iframe.className = `${device}-view`;
            });
        });

        // Fullscreen
        fullscreenBtn.addEventListener('click', function () {
            const fullscreenDiv = document.createElement('div');
            fullscreenDiv.className = 'fullscreen-preview';

            fullscreenDiv.innerHTML = `
                <div class="fullscreen-controls">
                    <span>Preview: {{ $project->title }}</span>
                    <button class="btn btn-sm btn-light" id="exitFullscreen"><i class="fas fa-times"></i> Exit</button>
                </div>
                <iframe></iframe>
            `;

            document.body.appendChild(fullscreenDiv);
            const fsIframe = fullscreenDiv.querySelector('iframe');
            renderPreview(fsIframe);

            fullscreenDiv.querySelector('#exitFullscreen').addEventListener('click', function () {
                fullscreenDiv.remove();
            });
        });
    });
</script>
@endpush
@endsection
