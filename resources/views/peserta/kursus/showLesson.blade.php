@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h4>{{ $lesson->meeting->judul ?? '-' }} (Pertemuan {{ $lesson->meeting->pertemuan ?? '-' }})</h4>

            {{-- Konten --}}
            <div class="mb-4">
                {!! $lesson->konten !!}
            </div>

            {{-- Video Section --}}
            @php
                $videoUrls = [$lesson->video_url1, $lesson->video_url2, $lesson->video_url3];
            @endphp
            @if(collect($videoUrls)->filter()->isNotEmpty())
                <h5 class="mb-3">Video Materi</h5>
                @foreach ($videoUrls as $index => $url)
                    @if($url)
                        <div class="mb-3">
                            <iframe 
                                src="{{ convertToEmbed($url) }}" 
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                allowfullscreen 
                                sandbox="allow-scripts allow-same-origin allow-presentation">
                            </iframe>
                            <p class="text-muted mt-2">Video {{ $index + 1 }}</p>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- File Materi Section --}}
            @php
                $fileMateri = [
                    $lesson->file_materi1, $lesson->file_materi2, $lesson->file_materi3,
                    $lesson->file_materi4, $lesson->file_materi5
                ];
            @endphp
            @if(collect($fileMateri)->filter()->isNotEmpty())
                <h5 class="mt-4 mb-3">File Materi</h5>
                @foreach ($fileMateri as $file)
                    @if($file)
                        <div class="mb-3">
                            <iframe 
                                src="{{ convertToPreview($file) }}" 
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                sandbox="allow-scripts allow-same-origin allow-presentation">
                            </iframe>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- Attachment Section --}}
            @php
                $attachmentUrls = [
                    $lesson->attachment_url1 => 'Lampiran 1',
                    $lesson->attachment_url2 => 'Lampiran 2',
                    $lesson->attachment_url3 => 'Lampiran 3'
                ];
            @endphp
            @if(collect(array_keys($attachmentUrls))->filter()->isNotEmpty())
                <h5 class="mt-4 mb-3">Lampiran Terkait</h5>
                <div class="row">
                    @foreach ($attachmentUrls as $url => $label)
                        @if($url)
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
                </div>
            @endif
        </div>
    </div>

    {{-- Kembali ke halaman kursus --}}
    <a href="{{ route('courses.showPeserta', $course->id) }}" class="btn btn-secondary mt-3">Kembali ke Kursus</a>
</div>
@endsection
