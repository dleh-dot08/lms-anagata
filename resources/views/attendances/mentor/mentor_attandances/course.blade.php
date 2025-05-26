@extends('layouts.mentor.template')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Daftar Kursus Anda</h2>
            <p class="text-muted">Kelola pertemuan dan absensi untuk kursus yang Anda ampu</p>
        </div>
    </div>

    @if($courses->isEmpty())
        <div class="text-center py-5 my-4 bg-light rounded-3">
            <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
            <h4>Tidak ada kursus yang tersedia</h4>
            <p class="text-muted">Saat ini Anda belum memiliki kursus yang dapat diakses</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="course-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                                    <i class="bi bi-book-fill fs-4 text-white"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-0">{{ $course->nama_kelas }}</h5>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 text-muted me-2"></i>
                                    <span class="text-muted small">Periode Kursus</span>
                                </div>
                                <p class="mb-0 ps-4">
                                    {{ \Carbon\Carbon::parse($course->waktu_mulai)->translatedFormat('d M Y') }}
                                    - {{ \Carbon\Carbon::parse($course->waktu_akhir)->translatedFormat('d M Y') }}
                                </p>
                            </div>

                            {{-- Cek apakah mentor sudah absen hari ini (tetap ada tapi tidak ditampilkan) --}}
                            @php
                                $sudahAbsen = \App\Models\Attendance::where('user_id', auth()->id())
                                    ->where('course_id', $course->id)
                                    ->whereDate('tanggal', \Carbon\Carbon::today())
                                    ->exists();
                            @endphp

                            <div class="mt-auto pt-3">
                                <a href="{{ route('mentor.absen.meetings', $course->id) }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-event me-2"></i> Lihat Pertemuan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .course-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
