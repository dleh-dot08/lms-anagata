@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $course->nama_kelas }}</h3>
        <!-- Tombol Rekap Nilai -->
        <a href="{{ route('mentor.scores.recap', $course->id) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-bar-graph"></i> Rekap Nilai
        </a>
    </div>

    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'scores'])

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
                    <div>
                        <a href="{{ route('mentor.scores.input', ['course' => $course->id, 'meeting' => $meeting->id]) }}" class="btn btn-primary">
                            Nilai
                        </a>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-muted">Belum ada pertemuan yang tersedia.</p>
        @endforelse
    </div>
</div>
@endsection
