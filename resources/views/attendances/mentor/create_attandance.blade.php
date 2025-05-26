@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Absen Siswa</h4>
            <p class="text-muted mb-0">
                {{ $course->nama_kelas }} - Pertemuan {{ $meeting->pertemuan }}
                ({{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->format('d F Y') }})
            </p>
        </div>
        <a href="{{ route('mentor.attendances.select_meeting', $course->id) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <form action="{{ route('mentor.attendances.store') }}" method="POST">
        @csrf
        <input type="hidden" name="course_id" value="{{ $course->id }}">
        <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
        <input type="hidden" name="tanggal" value="{{ now()->toDateString() }}">
    
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Daftar Siswa</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Nama Siswa</th>
                                <th width="20%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $enrollment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $enrollment->user->name }}</td>
                                <td>
                                    <select name="status[{{ $enrollment->id }}]" 
                                        class="form-select form-select-sm status-select" required>
                                    @php
                                        $existingStatus = $enrollment->attendances->first()?->status;
                                    @endphp
                                    <option value="Hadir" {{ $existingStatus === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="Izin" {{ $existingStatus === 'Izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="Sakit" {{ $existingStatus === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Tidak Hadir" {{ $existingStatus === 'Tidak Hadir' ? 'selected' : '' }}>Alpha</option>
                                </select>
                                @php
                                $existingWaktuAbsen = $enrollment->attendances->first()?->waktu_absen ?? now();
                                @endphp
                                <input type="hidden" name="waktu_absen[{{ $enrollment->id }}]" 
                                   value="{{ $existingWaktuAbsen }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    
        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Simpan Absensi
            </button>
        </div>
    </form>
    
</div>

@push('scripts')
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const keteranganInput = this.closest('tr').querySelector('.keterangan-input');
            if (this.value === 'izin' || this.value === 'sakit') {
                keteranganInput.disabled = false;
                keteranganInput.required = true;
            } else {
                keteranganInput.disabled = true;
                keteranganInput.required = false;
                keteranganInput.value = '';
            }
        });
    });
</script>
@endpush
@endsection