@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Laporan / Nilai /</span> Edit Nilai {{ $course->nama_kelas }}
        </h4>
    </div>
    <div class="mb-3">
        <a href="{{ route('admin.reports.nilai.show', $course->id) }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reports.nilai.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT')
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
                                    <div class="small text-muted">{{ $meeting->tanggal ? $meeting->tanggal->format('d/m/Y') : '-' }}</div>
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($course->meetings as $meeting)
                                <th class="text-center" style="min-width: 100px">Creativity</th>
                                <th class="text-center" style="min-width: 100px">Program</th>
                                <th class="text-center" style="min-width: 100px">Design</th>
                                <th class="text-center" style="min-width: 80px">Total</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->participants as $index => $participant)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $participant->name }}</td>
                                <td class="text-center">
                                    {{ $participant->sekolah ? 'Kelas ' . $participant->sekolah->jenjang->nama_jenjang : '-' }}
                                </td>
                                @foreach($course->meetings as $meeting)
                                    @php
                                        $score = App\Models\Score::where('peserta_id', $participant->id)
                                            ->where('meeting_id', $meeting->id)
                                            ->first();
                                    @endphp
                                    <td>
                                        <input type="number" step="0.1" min="0" max="100" 
                                            class="form-control form-control-sm"
                                            name="scores[{{ $participant->id }}][{{ $meeting->id }}][creativity_score]"
                                            value="{{ $score ? $score->creativity_score : '' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" min="0" max="100" 
                                            class="form-control form-control-sm"
                                            name="scores[{{ $participant->id }}][{{ $meeting->id }}][program_score]"
                                            value="{{ $score ? $score->program_score : '' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" min="0" max="100" 
                                            class="form-control form-control-sm"
                                            name="scores[{{ $participant->id }}][{{ $meeting->id }}][design_score]"
                                            value="{{ $score ? $score->design_score : '' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.1" min="0" max="100" 
                                            class="form-control form-control-sm"
                                            style="width: 80px"
                                            name="scores[{{ $participant->id }}][{{ $meeting->id }}][total_score]"
                                            value="{{ $score ? $score->total_score : '' }}"
                                            readonly>
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate total score
    function calculateTotal(row) {
        const creativity = parseFloat(row.querySelector('input[name*="[creativity_score]"]').value) || 0;
        const program = parseFloat(row.querySelector('input[name*="[program_score]"]').value) || 0;
        const design = parseFloat(row.querySelector('input[name*="[design_score]"]').value) || 0;
        
        let count = 0;
        let total = 0;
        
        if (creativity > 0) { total += creativity; count++; }
        if (program > 0) { total += program; count++; }
        if (design > 0) { total += design; count++; }
        
        const totalInput = row.querySelector('input[name*="[total_score]"]');
        totalInput.value = count > 0 ? (total / count).toFixed(1) : '';
    }

    // Add event listeners to all score inputs
    document.querySelectorAll('input[type="number"]:not([readonly])').forEach(input => {
        input.addEventListener('input', function() {
            calculateTotal(this.closest('tr'));
        });
    });
});
</script>
@endpush
@endsection