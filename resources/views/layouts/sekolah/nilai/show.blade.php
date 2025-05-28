@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Laporan / Nilai /</span> {{ $course->nama_kelas }}
        </h4>
    </div>
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <a href="{{ route('sekolah.reports.nilai.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
        <div>
            <a href="{{ route('sekolah.reports.nilai.export-pdf', $course->id) }}" class="btn btn-danger">
                <i class="bx bx-file me-1"></i> Export PDF
            </a>
            <a href="{{ route('sekolah.reports.nilai.export', $course->id) }}" class="btn btn-success">
                <i class="bx bx-download me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Unik:</strong> {{ $course->kode_unik ?? 'Tidak Ada' }}</p>
                    <p><strong>Mentor:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
                    <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</p>
                    <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</p>
                    <p><strong>Kelas:</strong> {{ $course->kelas->nama ?? 'Tidak Ada' }}</p>
                    <p><strong>Status:</strong> {{ $course->status }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Sekolah:</strong> {{ $course->sekolah->nama_sekolah ?? 'Tidak Ada' }}</p>
                    <p><strong>Program:</strong> {{ $course->program->nama_program ?? 'Tidak Ada' }}</p>
                    <p><strong>Level:</strong> {{ $course->level }}</p>
                    <p><strong>Deskripsi:</strong> {{ $course->deskripsi }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-center align-middle" style="width: 5%">No</th>
                            <th rowspan="2" class="text-center align-middle" style="width: 20%">Nama Siswa</th>
                            <th rowspan="2" class="text-center align-middle" style="width: 10%">Kelas</th>
                            @foreach($course->meetings as $meeting)
                            <th colspan="4" class="text-center">
                                Pertemuan {{ $loop->iteration }}
                                <div class="small text-muted">{{ $meeting->tanggal_pelaksanaan ? $meeting->tanggal_pelaksanaan->format('d/m/Y') : '-' }}</div>
                            </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($course->meetings as $meeting)
                            <th class="text-center">Creativity</th>
                            <th class="text-center">Program</th>
                            <th class="text-center">Design</th>
                            <th class="text-center">Total</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($course->enrollments as $index => $enrollment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $enrollment->user->name }}</td>
                            <td class="text-center">
                                {{ $enrollment->user->kelas ? $enrollment->user->kelas->nama : '-' }}
                            </td>
                            @foreach($course->meetings as $meeting)
                                @php
                                    $score = App\Models\Score::where('peserta_id', $enrollment->user->id)
                                        ->where('meeting_id', $meeting->id)
                                        ->first();
                                @endphp
                                <td class="text-center">{{ ($score && $score->creativity_score !== null) ? number_format($score->creativity_score, 1) : '-' }}</td>
                                <td class="text-center">{{ ($score && $score->program_score !== null) ? number_format($score->program_score, 1) : '-' }}</td>
                                <td class="text-center">{{ ($score && $score->design_score !== null) ? number_format($score->design_score, 1) : '-' }}</td>
                                <td class="text-center">{{ ($score && $score->total_score !== null) ? number_format($score->total_score, 1) : '-' }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 