@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Absensi</h2>

    {{-- Filter Tanggal --}}
    <form method="GET" action="{{ route('attendances.admin.index') }}" class="row g-3 mb-4">
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
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-select">
                <option value="">Semua</option>
                <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                <option value="peserta" {{ request('role') == 'peserta' ? 'selected' : '' }}>Peserta</option>
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
                    <th>Nama</th>
                    <th>Role</th>
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
                            @if($attendance->user->role_id == 2)
                                <span class="badge bg-info">Mentor</span>
                            @elseif($attendance->user->role_id == 3)
                                <span class="badge bg-success">Peserta</span>
                            @endif
                        </td>
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
                            <a href="{{ route('attendances.admin.show', $attendance->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->onEachSide(1)->links('pagination.custom') }}
    </div>
</div>
@endsection
