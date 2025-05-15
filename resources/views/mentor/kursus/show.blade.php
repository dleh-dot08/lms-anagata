@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-0">{{ $course->nama_kelas }}</h3>
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('mentor/kursus/'.$course->id.'/overview') ? 'active' : '' }}" href="{{ route('kursus.mentor.overview', $course->id) }}">Overview</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Meetings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Modul</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Assignment</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Project</a>
        </li>
    </ul>

    <div class="d-flex flex-column gap-3">
        @forelse ($course->meetings->sortBy('pertemuan') as $meeting)
            <a href="{{ route('kursus.pertemuan.show', [$course->id, $meeting->id]) }}" class="text-decoration-none text-dark">
                <div class="p-3 bg-light rounded shadow-sm hover-shadow-sm" style="transition: all 0.2s ease;">
                    <p class="mb-1"><strong>Pertemuan Ke-{{ $meeting->pertemuan }}</strong></p>
                    <p class="mb-1">Judul: {{ $meeting->judul }}</p>
                    <p class="mb-0 text-muted">
                        {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d M Y') }}
                        @if($meeting->waktu_mulai && $meeting->waktu_selesai)
                            — {{ \Carbon\Carbon::parse($meeting->waktu_mulai)->format('H.i') }} -
                            {{ \Carbon\Carbon::parse($meeting->waktu_selesai)->format('H.i') }}
                        @endif
                    </p>
                </div>
            </a>
        @empty
            <p class="text-muted">Belum ada pertemuan yang tersedia.</p>
        @endforelse
    </div>
</div>
@endsection
