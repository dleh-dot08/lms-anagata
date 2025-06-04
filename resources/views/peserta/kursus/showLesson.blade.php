@extends('layouts.peserta.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('courses.showPeserta', $course->id) }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Kursus
                </a>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary rounded-pill me-2">{{ $course->nama_kelas }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="pertemuanDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-calendar-event me-1"></i> Navigasi Pertemuan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="pertemuanDropdown">
                            @foreach ($course->meetings->sortBy('pertemuan') as $m)
                            @php $lessonId = $m->lesson->id ?? null; @endphp
                            @if ($lessonId)
                                <li>
                                    <a class="dropdown-item {{ $m->id == $lesson->meeting->id ? 'active bg-light fw-bold' : '' }}"
                                        href="{{ route('courses.showLesson', [$course->id, $lessonId]) }}">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-{{ $m->id == $lesson->meeting->id ? 'primary' : 'secondary' }} rounded-circle me-2">
                                                {{ $m->pertemuan }}
                                            </span>
                                            {{ $m->judul ?? 'Pertemuan '.$m->pertemuan }}
                                        </div>
                                    </a>
                                </li>
                            @endif
                        @endforeach                        
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Pertemuan Ke-{{ $lesson->meeting->pertemuan }}</h3>
                            <h5 class="mb-0 fw-normal">{{ $lesson->meeting->judul ?? 'Materi Pembelajaran' }}</h5>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date fs-5 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($lesson->meeting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <ul class="nav nav-tabs nav-fill mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="materi-tab" data-bs-toggle="tab" data-bs-target="#materi" type="button" role="tab" aria-controls="materi" aria-selected="true">
                                <i class="bi bi-book me-1"></i> Rangkuman Materi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab" aria-controls="video" aria-selected="false">
                                <i class="bi bi-play-circle me-1"></i> Video Materi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#file" type="button" role="tab" aria-controls="file" aria-selected="false">
                                <i class="bi bi-file-earmark-text me-1"></i> E-Modul
                            </button>
                        </li>
                    </ul>

                    @if ($lesson)
                        <div class="tab-content" id="myTabContent">
                            <!-- Tab Materi -->
                            <div class="tab-pane fade show active" id="materi" role="tabpanel" aria-labelledby="materi-tab">
                                <div class="p-3 bg-light rounded">
                                    <div class="content-wrapper">
                                        {!! $lesson->konten !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Video -->
                            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
                                <div class="row">
                                    @php $videoCount = 0; @endphp
                                    @foreach ([$lesson->video_url1, $lesson->video_url2, $lesson->video_url3] as $index => $url)
                                        @if($url)
                                            @php $videoCount++; @endphp
                                            <div class="col-12 mb-4">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-header bg-white">
                                                        <h6 class="mb-0"><i class="bi bi-play-circle-fill text-danger me-2"></i>Video {{ $videoCount }}</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="ratio ratio-16x9">
                                                            <iframe 
                                                                src="{{ convertToEmbed($url) }}" 
                                                                allowfullscreen 
                                                                sandbox="allow-scripts allow-same-origin allow-presentation">
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if($videoCount == 0)
                                        <div class="col-12 text-center py-5">
                                            <i class="bi bi-camera-video-off text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3">Belum ada video materi untuk pertemuan ini.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tab File -->
                            <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="file-tab">
                                <div class="row">
                                    @php $fileCount = 0; @endphp
                                    @foreach ([$lesson->file_materi1, $lesson->file_materi2, $lesson->file_materi3, $lesson->file_materi4, $lesson->file_materi5] as $index => $file)
                                        @if($file)
                                            @php $fileCount++; @endphp
                                            <div class="col-12 mb-4">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-header bg-white d-flex align-items-center">
                                                        <h6 class="mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Dokumen {{ $fileCount }}</h6>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="ratio ratio-16x9">
                                                            <iframe 
                                                                src="{{ convertToPreview($file) }}" 
                                                                sandbox="allow-scripts allow-same-origin allow-presentation">
                                                            </iframe>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if($fileCount == 0)
                                        <div class="col-12 text-center py-5">
                                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3">Belum ada file materi untuk pertemuan ini.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Attachment Section -->
                            <div class="tab-pane fade" id="lampiran" role="tabpanel" aria-labelledby="lampiran-tab">
                                <div class="row">
                                    @php
                                        $attachmentUrls = [
                                            $lesson->attachment_url1 => 'Lampiran 1',
                                            $lesson->attachment_url2 => 'Lampiran 2',
                                            $lesson->attachment_url3 => 'Lampiran 3'
                                        ];
                                        $attachmentCount = 0;
                                    @endphp
                                    @foreach ($attachmentUrls as $url => $label)
                                        @if($url)
                                            @php $attachmentCount++; @endphp
                                            <div class="col-md-4 mb-3">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="card-body d-flex flex-column justify-content-between">
                                                        <h6 class="card-title">{{ $label }}</h6>
                                                        <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary mt-2">
                                                            Buka Lampiran
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if($attachmentCount == 0)
                                        <div class="col-12 text-center py-5">
                                            <i class="bi bi-paperclip text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-3">Belum ada lampiran untuk pertemuan ini.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-4">Belum ada materi untuk pertemuan ini</h5>
                            <p class="text-muted">Materi akan ditampilkan di sini setelah ditambahkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-wrapper {
        font-size: 1.1rem;
        line-height: 1.6;
    }
    
    .content-wrapper h1, .content-wrapper h2, .content-wrapper h3, 
    .content-wrapper h4, .content-wrapper h5, .content-wrapper h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
    }
    
    .content-wrapper img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .content-wrapper table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }
    
    .content-wrapper table th,
    .content-wrapper table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .content-wrapper table th {
        background-color: #f8f9fa;
    }
    
    .content-wrapper blockquote {
        padding: 1rem 1.25rem;
        border-left: 5px solid #007bff;
        background-color: #f8f9fa;
        margin: 1.5rem 0;
    }
    
    .content-wrapper code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    
    .content-wrapper pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    
    .content-wrapper ul, .content-wrapper ol {
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    
    .content-wrapper a {
        color: #007bff;
        text-decoration: none;
    }
    
    .content-wrapper a:hover {
        text-decoration: underline;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background-color: transparent;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: rgba(0, 123, 255, 0.05);
        border-bottom: 3px solid rgba(0, 123, 255, 0.2);
    }
    @media (max-width: 768px) {
    .content-wrapper {
        font-size: 1rem;
        line-height: 1.5;
    }

    .nav-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }

    .card-header h3,
    .card-header h5 {
        font-size: 1.25rem;
    }

    .card-header .fw-bold {
        font-size: 0.9rem;
    }

    .ratio {
        height: auto;
    }

    iframe {
        width: 100%;
        height: auto;
    }

    .dropdown-menu {
        width: 100%;
        min-width: auto;
    }

    .dropdown-menu .dropdown-item {
        white-space: normal;
    }
}

@media (max-width: 576px) {
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }

    .card-header .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: flex-start;
    }

    .nav-tabs.nav-fill .nav-item {
        width: 100%;
        text-align: center;
    }

    .nav-tabs .nav-link {
        width: 100%;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.6em;
    }
}

</style>
@endsection
