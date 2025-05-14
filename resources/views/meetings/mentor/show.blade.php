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

        {{-- Header Pertemuan --}}
        <h4>Pertemuan Ke-{{ $meeting->pertemuan }}</h4>
        <p class="mb-4"><strong>Waktu:</strong> 
            {{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('l, d F Y') }} 
            {{ \Carbon\Carbon::parse($meeting->jam_mulai)->format('H:i') }} -
            {{ \Carbon\Carbon::parse($meeting->jam_selesai)->format('H:i') }}
        </p>

        {{-- Materi Pembelajaran --}}
        @if ($lesson)
            <h5 class="mb-3">Materi Pembelajaran</h5>
            <div class="mb-4">
                {!! $lesson->konten !!}
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
