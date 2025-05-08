@extends('layouts.peserta.template')

@section('content')
<div class="container py-4">
    <!-- Project Header with Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold text-primary mb-1">{{ $project->title }}</h2>
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-info text-dark">Kursus: {{ $project->course->nama_kelas ?? '-' }}</span>
                <span class="badge bg-secondary">Dibuat: {{ $project->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-muted">{{ $project->description ?? 'Tidak ada deskripsi' }}</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-start">
            <div class="btn-group">
                <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Project
                </a>
                <a href="{{ route('projects.peserta.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Preview and Code Tabs -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-header p-0 bg-light border-bottom-0">
            <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="preview-tab" data-bs-toggle="tab" data-bs-target="#preview-pane" type="button" role="tab" aria-controls="preview-pane" aria-selected="true">
                        <i class="fas fa-play-circle me-1"></i> Preview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="code-tab" data-bs-toggle="tab" data-bs-target="#code-pane" type="button" role="tab" aria-controls="code-pane" aria-selected="false">
                        <i class="fas fa-code me-1"></i> Code
                    </button>
                </li>
                <li class="nav-item ms-auto">
                    <button class="btn btn-sm btn-outline-primary mt-1 me-2" id="fullscreenBtn">
                        <i class="fas fa-expand-arrows-alt"></i> Full Screen
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="projectTabContent">
                <!-- Preview Tab -->
                <div class="tab-pane fade show active" id="preview-pane" role="tabpanel" aria-labelledby="preview-tab">
                    <div class="preview-container position-relative">
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                            <div class="device-selector">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary active" data-device="desktop">
                                        <i class="fas fa-desktop"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-device="tablet">
                                        <i class="fas fa-tablet-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-device="mobile">
                                        <i class="fas fa-mobile-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-success" id="refreshPreview">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                        <div class="iframe-container">
                            <iframe id="preview" class="desktop-view" style="width: 100%; height: 600px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
                
                <!-- Code Tab -->
                <div class="tab-pane fade" id="code-pane" role="tabpanel" aria-labelledby="code-tab">
                    <div class="code-container">
                        <ul class="nav nav-pills p-2 bg-light" id="codeSubTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="html-tab" data-bs-toggle="pill" data-bs-target="#html-code" type="button" role="tab" aria-controls="html-code" aria-selected="true">
                                    <i class="fab fa-html5 text-danger me-1"></i> HTML
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="css-tab" data-bs-toggle="pill" data-bs-target="#css-code" type="button" role="tab" aria-controls="css-code" aria-selected="false">
                                    <i class="fab fa-css3-alt text-primary me-1"></i> CSS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="js-tab" data-bs-toggle="pill" data-bs-target="#js-code" type="button" role="tab" aria-controls="js-code" aria-selected="false">
                                    <i class="fab fa-js-square text-warning me-1"></i> JavaScript
                                </button>
                            </li>
                            <li class="nav-item ms-auto">
                                <button class="btn btn-sm btn-outline-secondary" id="copyCodeBtn">
                                    <i class="fas fa-copy me-1"></i> Copy Current
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="codeSubTabContent">
                            <div class="tab-pane fade show active" id="html-code" role="tabpanel" aria-labelledby="html-tab">
                                <div class="code-editor p-3 bg-dark text-light">
                                    <pre class="mb-0" style="white-space: pre-wrap; max-height: 500px; overflow-y: auto;"><code class="language-html">{{ $project->html_code }}</code></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="css-code" role="tabpanel" aria-labelledby="css-tab">
                                <div class="code-editor p-3 bg-dark text-light">
                                    <pre class="mb-0" style="white-space: pre-wrap; max-height: 500px; overflow-y: auto;"><code class="language-css">{{ $project->css_code }}</code></pre>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="js-code" role="tabpanel" aria-labelledby="js-tab">
                                <div class="code-editor p-3 bg-dark text-light">
                                    <pre class="mb-0" style="white-space: pre-wrap; max-height: 500px; overflow-y: auto;"><code class="language-javascript">{{ $project->js_code }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Project Info -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Project Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Created:</strong> {{ $project->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $project->updated_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                    <p><strong>Author:</strong> {{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .code-editor {
        border-radius: 0 0 0.25rem 0.25rem;
        font-family: 'Courier New', monospace;
    }
    
    .device-selector .btn-group .btn.active {
        background-color: #6c757d;
        color: white;
    }
    
    .iframe-container {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .desktop-view {
        width: 100%;
    }
    
    .tablet-view {
        width: 768px;
        margin: 0 auto;
        display: block;
        border: 10px solid #ddd;
        border-radius: 10px;
    }
    
    .mobile-view {
        width: 375px;
        margin: 0 auto;
        display: block;
        border: 10px solid #ddd;
        border-radius: 20px;
    }
    
    .fullscreen-preview {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        background: white;
    }
    
    .fullscreen-preview iframe {
        height: calc(100vh - 50px);
    }
    
    .fullscreen-controls {
        height: 50px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
    }
    
    .nav-tabs .nav-link, .nav-pills .nav-link {
        display: flex;
        align-items: center;
    }
    
    .badge {
        font-weight: 500;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const iframe = document.getElementById('preview');
        const deviceButtons = document.querySelectorAll('.device-selector .btn');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const refreshBtn = document.getElementById('refreshPreview');
        const copyCodeBtn = document.getElementById('copyCodeBtn');

        const htmlCode = {!! json_encode($project->html_code) !!};
        const cssCode = {!! json_encode($project->css_code) !!};
        const jsCode = {!! json_encode($project->js_code) !!};

        function renderPreview(targetIframe = iframe) {
            const iframeDoc = targetIframe.contentDocument || targetIframe.contentWindow.document;

            iframeDoc.open();
            iframeDoc.write(`
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
            iframeDoc.close();
        }

        renderPreview();

        deviceButtons.forEach(button => {
            button.addEventListener('click', function () {
                const device = this.getAttribute('data-device');

                deviceButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                iframe.classList.remove('desktop-view', 'tablet-view', 'mobile-view');
                iframe.classList.add(`${device}-view`);
            });
        });

        refreshBtn.addEventListener('click', function () {
            renderPreview();
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            setTimeout(() => {
                icon.classList.remove('fa-spin');
            }, 500);
        });

        // Fullscreen logic
        let isFullscreen = false;
        fullscreenBtn.addEventListener('click', function () {
            if (isFullscreen) return;

            isFullscreen = true;

            const fullscreenDiv = document.createElement('div');
            fullscreenDiv.className = 'fullscreen-preview';

            fullscreenDiv.innerHTML = `
                <div class="fullscreen-controls">
                    <h5 class="mb-0">Preview: {{ $project->title }}</h5>
                    <button id="exitFullscreen" class="btn btn-sm btn-danger">
                        <i class="fas fa-times me-1"></i> Exit Fullscreen
                    </button>
                </div>
                <iframe style="width: 100%; border: none;" class="desktop-view"></iframe>
            `;

            document.body.appendChild(fullscreenDiv);

            const fullscreenIframe = fullscreenDiv.querySelector('iframe');
            renderPreview(fullscreenIframe);

            fullscreenDiv.querySelector('#exitFullscreen').addEventListener('click', function () {
                fullscreenDiv.remove();
                isFullscreen = false;
            });
        });

        // Copy current tab code
        copyCodeBtn.addEventListener('click', function () {
            const activeTab = document.querySelector('#codeSubTabContent .tab-pane.active code');
            const text = activeTab.innerText;

            navigator.clipboard.writeText(text).then(() => {
                alert('Kode berhasil disalin!');
            }).catch(err => {
                alert('Gagal menyalin kode.');
            });
        });
    });
</script>
@endsection