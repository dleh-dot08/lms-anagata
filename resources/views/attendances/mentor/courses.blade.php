@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold">Daftar Kelas yang Belum Diabsen Hari Ini</h4>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nama Kelas</th>
                    <th>Mentor</th>
                    <th>Kategori</th>
                    <th>Jenjang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $index => $course)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $course->nama_kelas }}</td>
                        <td>{{ $course->mentor->name ?? '-' }}</td>
                        <td>{{ $course->kategori->nama_kategori }}</td>
                        <td>{{ $course->jenjang->nama_jenjang }}</td>
                        <td><span class="badge bg-warning text-dark">Belum Absen</span></td>
                        <td>
                            <a href="{{ route('attendances.create', $course->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>Absen
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <span class="text-success fs-5">Semua kelas sudah diabsen hari ini 🎉</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h4 class="mt-5 mb-4 fw-bold">Riwayat Absensi</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Kelas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attendance->course->nama_kelas }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $status = strtolower($attendance->status);
                                $badgeClass = match($status) {
                                    'hadir' => 'success',
                                    'izin' => 'info',
                                    'sakit' => 'warning',
                                    'alpha' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($attendance->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <span class="text-muted">Belum ada riwayat absensi.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
