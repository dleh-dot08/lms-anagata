@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mentor Notes /</span> Note Details
    </h4>

    <!-- Meeting Info -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Pertemuan ke-{{ $meeting->pertemuan }}</h5>
            <p class="card-text">Date: {{ $meeting->tanggal_pelaksanaan->format('d M Y') }}</p>
            <p class="card-text">Course: {{ $meeting->course->nama_kelas }}</p>
            <p class="card-text">Mentor: {{ $meeting->course->mentor->name ?? 'Not Assigned' }}</p>
        </div>
    </div>

    <!-- Note Content -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Note Details</h5>
            <dl class="row">
                <dt class="col-sm-3">Materi</dt>
                <dd class="col-sm-9">{{ $note->materi ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Project</dt>
                <dd class="col-sm-9">{{ $note->project ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Sikap Siswa</dt>
                <dd class="col-sm-9">{{ $note->sikap_siswa ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Hambatan</dt>
                <dd class="col-sm-9">{{ $note->hambatan ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Solusi</dt>
                <dd class="col-sm-9">{{ $note->solusi ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Masukan</dt>
                <dd class="col-sm-9">{{ $note->masukan ?? 'No Data' }}</dd>

                <dt class="col-sm-3">Lain-lain</dt>
                <dd class="col-sm-9">{{ $note->lain_lain ?? 'No Data' }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection