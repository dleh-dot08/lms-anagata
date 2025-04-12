@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Absensi</h2>

    {{-- Filter --}}
    <form method="GET" action="{{ route('attendances.admin.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
        </div>
        <div class="col-md-4">
            <label for="course_id" class="form-label">Kelas / Kursus</label>
            <select name="course_id" id="course_id" class="form-select">
                <option value="">-- Semua Kursus --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Kursus</th>
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
                        <td>{{ $attendance->course->nama_kelas ?? '-' }}</td>
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
    <div class="d-flex justify-content-end">
        {{ $attendances->withQueryString()->links() }}
    </div>
</div>
@endsection
