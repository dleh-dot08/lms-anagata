@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">Kelas yang Kamu Ajar</h4>
    </div>

    @if($courses->isEmpty())
        <div class="alert alert-info">
            Kamu belum mengajar kelas apapun saat ini.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($courses as $course)
                <div class="col">
                    <a href="{{ route('mentor.notes.meetings', $course->id) }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm border-0 hover-shadow">
                            <div class="card-body">
                                <h5 class="card-title text-dark fw-semibold">{{ $course->nama_kelas }}</h5>
                                <p class="card-text mb-1 text-muted"><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($course->waktu_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($course->waktu_akhir)->format('d M Y') }}</p>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    <span class="badge bg-primary">{{ $course->mentor->name }}</span>
                                    @if($course->mentor2)
                                        <span class="badge bg-info">{{ $course->mentor2->name }}</span>
                                    @endif
                                    @if($course->mentor3)
                                        <span class="badge bg-info">{{ $course->mentor3->name }}</span>
                                    @endif
                                </div>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                            <div class="card-footer bg-white border-0 text-end">
                                <button class="btn btn-outline-primary btn-sm">Isi Catatan <i class="bi bi-journal-text ms-1"></i></button>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
