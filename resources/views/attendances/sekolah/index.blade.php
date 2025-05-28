@extends('layouts.sekolah.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Laporan Absensi Siswa</h2>

    {{-- Filter Tanggal --}}
    <form method="GET" action="{{ route('attendances.sekolah.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
        </div>
        <div class="col-md-3">
            <label for="tipe" class="form-label">Jenis Absensi</label>
            <select name="tipe" id="tipe" class="form-select">
                <option value="">Semua</option>
                <option value="kursus" {{ request('tipe') == 'kursus' ? 'selected' : '' }}>Kursus</option>
                <option value="kegiatan" {{ request('tipe') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Status Kehadiran</label>
            <select name="status" id="status" class="form-select">
                <option value="">Semua</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Tidak Hadir" {{ request('status') == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
            </select>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>Kursus / Kegiatan</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}</td>
                        <td>{{ $attendance->user->name }}</td>
                        <td>    
                            @if($attendance->course_id && $attendance->course)
                                {{ $attendance->course->nama_kelas }}
                            @elseif($attendance->activity_id && $attendance->activity)
                                {{ $attendance->activity->nama_kegiatan }}
                            @else
                                <em>Tidak diketahui</em>
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                @if($attendance->status == 'Hadir') bg-success
                                @elseif($attendance->status == 'Izin') bg-warning
                                @elseif($attendance->status == 'Sakit') bg-primary
                                @else bg-danger @endif">
                                {{ $attendance->status }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('attendances.sekolah.show', $attendance->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $attendances->onEachSide(1)->links('pagination.custom') }}
    </div>
</div>
@endsection