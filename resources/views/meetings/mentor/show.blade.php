@extends('layouts.mentor.template')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">

        {{-- Tombol Kembali --}}
        <div class="mb-3">
            <a href="{{ route('mentor.kursus.show', $course->id) }}" class="btn btn-secondary">
                &larr; Kembali
            </a>
        </div>

        {{-- Dropdown Pilih Pertemuan --}}
        <div class="mb-4">
            <label for="pertemuanSelect" class="form-label">Pilih Pertemuan:</label>
            <select id="pertemuanSelect" class="form-select" onchange="location = this.value;">
                @foreach ($course->meetings as $m)
                    <option value="{{ route('kursus.pertemuan.show', [$course->id, $m->id]) }}"
                        {{ $m->id == $meeting->id ? 'selected' : '' }}>
                        Pertemuan Ke-{{ $m->pertemuan }} - {{ $m->judul ?? '-' }}
                    </option>
                @endforeach
            </select>
        </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Pertemuan Ke-{{ $meeting->pertemuan }}</h3>
                            <h5 class="mb-0 fw-normal">{{ $meeting->judul ?? 'Materi Pembelajaran' }}</h5>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date fs-5 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }}</div>
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

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Pertemuan Ke-{{ $meeting->pertemuan }}</h3>
                            <h5 class="mb-0 fw-normal">{{ $meeting->judul ?? 'Materi Pembelajaran' }}</h5>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date fs-5 me-2"></i>
                                <div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <ul class="nav nav-tabs nav-fill mb-4" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="materi-tab" data-bs-toggle="tab" data-bs-target="#materi" type="button" role="tab" aria-controls="materi" aria-selected="true">
                                <i class="bi bi-book me-1"></i> Detail Pembelajaran
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab" aria-controls="video" aria-selected="false">
                                <i class="bi bi-play-circle me-1"></i> Video Materi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#file" type="button" role="tab" aria-controls="file" aria-selected="false">
                                <i class="bi bi-file-earmark-text me-1"></i> File Materi
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
                                                                src="{{ $url }}" 
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
                                                        src="{{ $file }}" 
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
</style>
@endsection
