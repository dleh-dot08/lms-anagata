@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Absensi</h2>

    {{-- Filter Section - Dibungkus dalam Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary">
            <h5 class="mb-0 text-white">Filter Absensi</h5>
        </div>
        <div class="card-body mt-2">
            <form method="GET" action="{{ route('attendances.admin.index') }}" class="row g-3">
                {{-- Baris Filter Umum --}}
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-4">
                    <label for="tipe" class="form-label">Jenis Absensi</label>
                    <select name="tipe" id="tipe" class="form-select">
                        <option value="">Semua</option>
                        <option value="kursus" {{ request('tipe') == 'kursus' ? 'selected' : '' }}>Kursus</option>
                        <option value="kegiatan" {{ request('tipe') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">Semua</option>
                        <option value="mentor" {{ request('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="peserta" {{ request('role') == 'peserta' ? 'selected' : '' }}>Peserta</option>
                    </select>
                </div>

                {{-- Baris Filter Detail Peserta (Kelas, Sekolah, Jenjang) --}}
                <div class="col-md-4">
                    <label for="kelas" class="form-label">Kelas</label>
                    <select name="kelas" id="kelas" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="sekolah" class="form-label">Sekolah</label>
                    <select name="sekolah" id="sekolah" class="form-select">
                        <option value="">Semua Sekolah</option>
                        @foreach($sekolah as $s)
                            <option value="{{ $s->id }}" {{ request('sekolah') == $s->id ? 'selected' : '' }}>{{ $s->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="jenjang" class="form-label">Jenjang</label>
                    <select name="jenjang" id="jenjang" class="form-select">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjang as $j)
                            <option value="{{ $j->id }}" {{ request('jenjang') == $j->id ? 'selected' : '' }}>{{ $j->nama_jenjang }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Baris Filter Kursus & Tombol Aksi --}}
                <div class="col-md-4">
                    <label for="course_id" class="form-label">Kursus</label>
                    <select name="course_id" id="course_id" class="form-select">
                        <option value="">Semua Kursus</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                    {{-- Tombol Reset Filter --}}
                    <a href="{{ route('attendances.admin.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tombol Export Excel --}}
    <div class="mb-3 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportExcelModal">
            <i class="bi bi-file-earmark-excel"></i> Export Absensi
        </button>
    </div>

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
                    <th>Pengabsen</th>
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
                            @if($attendance->recordedByMentor)
                                {{ $attendance->recordedByMentor->name }}
                            @else
                                <em>Tidak diketahui</em>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('attendances.admin.show', $attendance->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data absensi.</td>
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

<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-labelledby="exportExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportExcelModalLabel">Export Absensi ke Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('attendances.admin.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_type" class="form-label">Pilih Jenis Export:</label>
                        <select class="form-select" id="export_type" name="export_type">
                            <option value="all_courses">Semua Kursus (tiap kursus di sheet terpisah)</option>
                            <option value="selected_course">Pilih Kursus</option>
                            <option value="all_schools">Semua Sekolah (tiap sekolah di sheet terpisah)</option>
                            <option value="selected_school">Pilih Sekolah</option>
                        </select>
                    </div>

                    <div class="mb-3" id="course_select_container" style="display: none;">
                        <label for="export_course_id" class="form-label">Pilih Kursus:</label>
                        <select class="form-select" id="export_course_id" name="export_course_id">
                            <option value="">-- Pilih Kursus --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="school_select_container" style="display: none;">
                        <label for="export_school_id" class="form-label">Pilih Sekolah:</label>
                        <select class="form-select" id="export_school_id" name="export_school_id">
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($sekolah as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="export_start_date" class="form-label">Tanggal Mulai (Opsional):</label>
                        <input type="date" class="form-control" id="export_start_date" name="export_start_date">
                    </div>
                    <div class="mb-3">
                        <label for="export_end_date" class="form-label">Tanggal Selesai (Opsional):</label>
                        <input type="date" class="form-control" id="export_end_date" name="export_end_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportTypeSelect = document.getElementById('export_type');
        const courseSelectContainer = document.getElementById('course_select_container');
        const schoolSelectContainer = document.getElementById('school_select_container');

        function toggleExportOptions() {
            const selectedType = exportTypeSelect.value;
            courseSelectContainer.style.display = 'none';
            schoolSelectContainer.style.display = 'none';

            if (selectedType === 'selected_course') {
                courseSelectContainer.style.display = 'block';
            } else if (selectedType === 'selected_school') {
                schoolSelectContainer.style.display = 'block';
            }
        }

        exportTypeSelect.addEventListener('change', toggleExportOptions);
        toggleExportOptions();
    });
</script>
@endpush
@endsection