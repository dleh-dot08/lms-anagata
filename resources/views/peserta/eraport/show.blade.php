@extends('layouts.peserta.template')

@section('content')
@php
    $snap = is_array($eraport->snapshot_json)
        ? $eraport->snapshot_json
        : (json_decode($eraport->snapshot_json, true) ?: []);

    $nama     = data_get($snap, 'student.name', '-');
    $kelas    = data_get($snap, 'student.kelas_label', '-');
    $semester = data_get($snap, 'semester.label', '-');

    $program  = data_get($snap, 'course.title', '-');
    $platform = data_get($snap, 'course.platform', '-');
    $category = data_get($snap, 'course.category', '-');

    $avgProject = data_get($snap, 'scores.avg_project', '-');
    $logicCT    = data_get($snap, 'scores.logic_ct', '-');
    $creativity = data_get($snap, 'scores.creativity', '-');

    $hadir = data_get($snap, 'attendance.summary.hadir', '-');
    $sakit = data_get($snap, 'attendance.summary.sakit', '-');
    $izin  = data_get($snap, 'attendance.summary.izin', '-');
    $alpha = data_get($snap, 'attendance.summary.alpha', '-');

    $mentorNote = data_get($snap, 'mentor_note.note', '-');

    $reportNumberSnap = data_get($snap, 'eraport.number');
    $verifyUrlSnap    = data_get($snap, 'eraport.verify_url');

    $reportNumber = $reportNumberSnap ?? $eraport->report_number ?? ('ERAPORT-' . $eraport->id);
@endphp

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Detail E-Raport</h4>
            <small class="text-muted">Nomor: {{ $reportNumber }}</small>
        </div>
        <a href="{{ route('peserta.eraport.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4"><b>Nama:</b> {{ $nama }}</div>
                <div class="col-md-4"><b>Kelas:</b> {{ $kelas }}</div>
                <div class="col-md-4"><b>Semester:</b> {{ $semester }}</div>

                <div class="col-md-4"><b>Program/Kursus:</b> {{ $program }}</div>
                <div class="col-md-4"><b>Platform:</b> {{ $platform }}</div>
                <div class="col-md-4"><b>Kategori:</b> {{ $category }}</div>

                <div class="col-md-4"><b>Versi:</b> {{ $eraport->version ?? '-' }}</div>
            </div>

            <hr>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('peserta.eraport.download', $eraport) }}"
                   class="btn btn-primary {{ $eraport->pdf_path ? '' : 'disabled' }}">
                    Download PDF
                </a>

                {{-- Pakai route token (kalau ada verify_token di DB) --}}
                @if(!empty($eraport->verify_token))
                    <a href="{{ route('public.eraport.verify', ['token' => $eraport->verify_token]) }}"
                       target="_blank"
                       class="btn btn-outline-success">
                        Cek Validasi
                    </a>
                {{-- Fallback: pakai verify_url dari snapshot --}}
                @elseif(!empty($verifyUrlSnap))
                    <a href="{{ $verifyUrlSnap }}" target="_blank" class="btn btn-outline-success">
                        Cek Validasi
                    </a>
                @endif

                {{-- OPTIONAL: kalau ada pdf_path --}}
                @if(!empty($eraport->pdf_path))
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($eraport->pdf_path) }}"
                       target="_blank"
                       class="btn btn-outline-primary">
                       Lihat Sertifikat
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light fw-semibold">Komponen Penilaian</div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <div class="text-muted small">Nilai Rata-Rata Proyek Digital</div>
                        <div class="fs-4 fw-semibold">{{ $avgProject }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <div class="text-muted small">Pemahaman Logika/Algoritma & CT</div>
                        <div class="fs-4 fw-semibold">{{ $logicCT }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <div class="text-muted small">Kreativitas</div>
                        <div class="fs-4 fw-semibold">{{ $creativity }}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-2">Rekap Kehadiran</div>
                        <div>Hadir: <b>{{ $hadir }}</b></div>
                        <div>Sakit: <b>{{ $sakit }}</b></div>
                        <div>Izin: <b>{{ $izin }}</b></div>
                        <div>Alpha: <b>{{ $alpha }}</b></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small mb-2">Catatan Mentor</div>
                        <div style="white-space: pre-wrap;">{{ $mentorNote }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
