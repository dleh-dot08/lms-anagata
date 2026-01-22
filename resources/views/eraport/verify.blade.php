<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verifikasi E-Raport</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@php
  $nama = data_get($snap, 'student.name', '-');
  $kelas = data_get($snap, 'student.kelas_label', '-');
  $semester = data_get($snap, 'semester.label', '-');
  $program = data_get($snap, 'course.title', '-');
  $nomor = data_get($snap, 'eraport.number') ?? $eraport->report_number ?? ('#'.$eraport->id);
@endphp

<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
          <h4 class="mb-1">Verifikasi E-Raport</h4>
          <div class="text-muted">Nomor: <b>{{ $nomor }}</b></div>
        </div>
        <span class="badge {{ $isValid ? 'text-bg-success' : 'text-bg-warning' }} p-2">
          {{ $isValid ? 'VALID (Published)' : 'Ditemukan, belum publish' }}
        </span>
      </div>

      <hr>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="border rounded p-3 bg-white">
            <div class="text-muted small">Nama</div>
            <div class="fw-semibold">{{ $nama }}</div>
            <div class="text-muted small mt-2">Kelas</div>
            <div class="fw-semibold">{{ $kelas }}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded p-3 bg-white">
            <div class="text-muted small">Semester</div>
            <div class="fw-semibold">{{ $semester }}</div>
            <div class="text-muted small mt-2">Program</div>
            <div class="fw-semibold">{{ $program }}</div>
          </div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2 flex-wrap">
        @if($pdfUrl)
          <a class="btn btn-primary" href="{{ $pdfUrl }}" target="_blank">Buka PDF</a>
        @endif
        @if(!empty($certificateUrl))
          <a class="btn btn-outline-success" href="{{ $certificateUrl }}" target="_blank">Lihat Sertifikat</a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ url('/') }}">Kembali</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
