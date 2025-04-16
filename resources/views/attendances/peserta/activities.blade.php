@extends('layouts.peserta.template')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold">Daftar Kegiatan yang Belum Diabsen Hari Ini</h4>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $index => $activity)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $activity->nama_kegiatan }}</td>
                        <td>{{ \Carbon\Carbon::parse($activity->pivot->tanggal_mulai)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($activity->pivot->tanggal_selesai)->format('d-m-Y') }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">Belum Absen</span>
                        </td>
                        <td>
                            <a href="{{ route('attendances.activity.create', $activity->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil-square me-1"></i>Absen
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <span class="text-success fs-5">Semua kegiatan sudah diabsen hari ini 🎉</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h4 class="mt-5 mb-4 fw-bold">Riwayat Absensi Kegiatan</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activityAttendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attendance->activity->nama_kegiatan }}</td>
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
                            <span class="text-muted">Belum ada riwayat absensi kegiatan.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
