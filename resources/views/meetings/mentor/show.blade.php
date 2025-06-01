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

            {{-- Video Materi --}}
            <h5 class="mb-3">Video Materi</h5>
            @foreach ([$lesson->video_url1, $lesson->video_url2, $lesson->video_url3] as $url)
                @if($url)
                    <div class="mb-3">
                        <iframe 
                            src="{{ $url }}" 
                            width="100%" 
                            height="400" 
                            frameborder="0" 
                            allowfullscreen 
                            sandbox="allow-scripts allow-same-origin allow-presentation">
                        </iframe>
                    </div>
                @endif
            @endforeach

            {{-- File Materi --}}
            <h5 class="mt-4 mb-3">File Materi</h5>
            @foreach ([$lesson->file_materi1, $lesson->file_materi2, $lesson->file_materi3, $lesson->file_materi4, $lesson->file_materi5] as $file)
                @if($file)
                    <div class="mb-3">
                        <iframe 
                            src="{{ $file }}" 
                            width="100%" 
                            height="400" 
                            frameborder="0" 
                            sandbox="allow-scripts allow-same-origin allow-presentation">
                        </iframe>
                    </div>
                @endif
            @endforeach
        @else
            <p><em>Belum ada materi untuk pertemuan ini.</em></p>
        @endif

    </div>
</div>
@endsection
