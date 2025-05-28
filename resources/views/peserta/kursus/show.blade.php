@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow mb-4 mt-4 border-0">
        <div class="card-header bg-primary py-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 text-white font-weight-bold">{{ $course->nama_kelas }}</h4>
            </div>
        </div>
        <div class="card-body">
            {{-- Cek status aktif --}}
            @if($course->status === 'Aktif' && now()->between($course->waktu_mulai, $course->waktu_akhir))
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3 border-0">
                            <div class="card-body py-3">
                                <h6 class="text-muted mb-3">Informasi Dasar</h6>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-fingerprint mr-2"></i>Kode Unik</div>
                                    <div>{{ $course->kode_unik ?? '-' }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-tag mr-2"></i>Kategori</div>
                                    <div>{{ $course->kategori->nama_kategori ?? '-' }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-layer-group mr-2"></i>Jenjang</div>
                                    <div>{{ $course->jenjang->nama_jenjang ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light mb-3 border-0">
                            <div class="card-body py-3">
                                <h6 class="text-muted mb-3">Detail Kursus</h6>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-signal mr-2"></i>Level</div>
                                    <div><span class="badge badge-info">{{ ucfirst($course->level) }}</span></div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-calendar-alt mr-2"></i>Mulai</div>
                                    <div>{{ \Carbon\Carbon::parse($course->waktu_mulai)->translatedFormat('d M Y') }}</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="text-primary" style="width: 120px;"><i class="fas fa-calendar-check mr-2"></i>Berakhir</div>
                                    <div>{{ \Carbon\Carbon::parse($course->waktu_akhir)->translatedFormat('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Deskripsi Kursus</h5>
                    </div>
                    <div class="card-body">
                        <div class="description-content">
                            {!! nl2br(e($course->deskripsi)) !!}
                        </div>
                    </div>
                </div>

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-book mr-2"></i>Materi Pembelajaran</h5>
                    </div>
                    <div class="card-body">
                        @if($course->lessons && count($course->lessons))
                        <div class="list-group">
                            @foreach ($course->lessons as $lesson)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-primary mr-2 text-dark">
                                            {{ $lesson->meeting->pertemuan ?? '-' }}
                                        </span>
                                        <span>{{ $lesson->meeting->judul ?? $lesson->judul }}</span>
                                    </div>
                                    <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye mr-1"></i> Lihat Materi
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>Belum ada materi tersedia untuk kursus ini.
                        </div>
                    @endif                    
                    </div>
                </div>

                {{-- Tambahan Section: Projects --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-project-diagram mr-2"></i>Project Saya</h5>
                        <a href="{{ route('projects.peserta.createCourse', ['course' => $course->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Buat Project
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $myProjects = $course->projects->where('user_id', auth()->id());
                        @endphp

                        @if($myProjects->count())
                            <div class="row">
                                @foreach($myProjects as $project)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 shadow-sm border-0 hover-card">
                                            <div class="card-body">
                                                <h6 class="card-title font-weight-bold">{{ $project->title }}</h6>
                                                <p class="card-text text-muted"><i class="fas fa-user mr-1"></i>{{ $project->user->name ?? 'Tidak diketahui' }}</p>
                                                <a href="{{ route('projects.peserta.show', $project->id) }}" class="btn btn-sm btn-outline-primary mt-2 stretched-link">
                                                    <i class="fas fa-eye mr-1"></i> Lihat Project
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border">
                                <i class="fas fa-info-circle mr-2"></i>Kamu belum membuat project untuk kursus ini.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <h5>Kursus Tidak Aktif</h5>
                    <p>Kursus ini sudah tidak aktif atau telah berakhir. Silakan hubungi admin jika kamu memerlukan akses ulang.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
