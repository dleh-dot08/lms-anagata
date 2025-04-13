@extends('layouts.peserta.template') <!-- Ganti dengan layout yang sesuai untuk peserta -->

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>{{ $lesson->judul }} (Pertemuan {{ $lesson->pertemuan_ke }})</h4>

            {{-- Konten --}}
            <div class="mb-4">
                {!! $lesson->konten !!}
            </div>

            {{-- Video Section --}}
            <h5 class="mb-3">Video Materi</h5>
            @foreach ([$lesson->video_url1, $lesson->video_url2, $lesson->video_url3] as $url)
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
                    </div>
                @endif
            @endforeach

            {{-- File Materi Section --}}
            <h5 class="mt-4 mb-3">File Materi</h5>
            @foreach ([$lesson->file_materi1, $lesson->file_materi2, $lesson->file_materi3, $lesson->file_materi4, $lesson->file_materi5] as $file)
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

        </div>
    </div>

    {{-- Kembali ke halaman kursus --}}
    <a href="{{ route('courses.showPeserta', $course->id) }}" class="btn btn-secondary mt-3">Kembali ke Kursus</a>
</div>
@endsection
