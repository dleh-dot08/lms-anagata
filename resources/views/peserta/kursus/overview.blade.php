@extends('layouts.peserta.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>
    @include('peserta.kursus.partials.nav-tabs', ['activeTab' => 'overview'])

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
                        <div><span class="badge badge-info text-dark">{{ ucfirst($course->level) }}</span></div>
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
</div>
@endsection