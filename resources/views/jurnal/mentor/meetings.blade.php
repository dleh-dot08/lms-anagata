@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">Daftar Pertemuan - {{ $course->nama_kelas }}</h4>
    </div>

    @if($course->meetings->isEmpty())
        <div class="alert alert-warning">
            Belum ada pertemuan yang tersedia untuk kelas ini.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($course->meetings as $meeting)
            <div class="card mb-3 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Pertemuan ke-{{ $meeting->pertemuan }}</strong>
                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($meeting->tanggal)->translatedFormat('d M Y') }}</small>
                    </div>
        
                    @if($meeting->note)
                        <div>
                            <a href="{{ route('mentor.notes.show', $meeting->id) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <a href="{{ route('mentor.notes.edit', $meeting->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    @else
                        <a href="{{ route('mentor.notes.create', $meeting->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-journal-plus"></i> Buat Catatan
                        </a>
                    @endif
                </div>
            </div>
        @endforeach        
        </div>
    @endif
</div>
@endsection
