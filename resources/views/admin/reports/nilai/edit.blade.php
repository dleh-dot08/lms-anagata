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
                    <style>
                        .table-wrapper {
                            position: relative;
                            overflow: auto;
                            background: white;
                            height: 600px;
                            border: 1px solid #dee2e6;
                            border-radius: 0.375rem;
                        }
                        /* Header container */
                        thead {
                            position: sticky;
                            top: 0;
                            z-index: 3;
                            background: #fcfdfd;
                        }
                        /* Sticky columns */
                        .sticky-col {
                            position: sticky;
                            background: white !important;
                            z-index: 2;
                        }
                        .first-col {
                            left: 0;
                            min-width: 80px;
                            width: 80px;
                        }
                        .second-col {
                            left: 80px;
                            min-width: 220px;
                            width: 220px;
                        }
                        .third-col {
                            left: 300px;
                            min-width: 120px;
                            width: 120px;
                        }
                        /* Header cells */
                        thead th {
                            background: #fcfdfd !important;
                        }
                        /* Sticky header cells */
                        thead .sticky-col {
                            z-index: 4;
                            background: #fcfdfd !important;
                        }
                        /* Borders */
                        .sticky-col {
                            border-right: 1px solid #dee2e6;
                        }
                        .third-col {
                            border-right: 2px solid #dee2e6;
                        }
                        .second-col, .third-col {
                            border-left: none;
                        }
                        /* Shadow for sticky columns */
                        .third-col::after {
                            content: '';
                            position: absolute;
                            top: 0;
                            right: -2px;
                            bottom: 0;
                            width: 4px;
                            pointer-events: none;
                            box-shadow: inset -2px 0 2px -2px rgba(0,0,0,0.1);
                        }
                        /* Table body cells */
                        tbody td {
                            background: white !important;
                        }
                        /* Add padding after frozen columns */
                        .table td:nth-child(4),
                        .table th:nth-child(4) {
                            padding-left: 20px;
                        }
                        /* Save button container */
                        .save-button-container {
                            position: sticky;
                            bottom: 0;
                            background: white;
                            padding: 1rem;
                            border-top: 1px solid #dee2e6;
                            z-index: 5;
                            margin-top: 1rem;
                            text-align: right;
                        }
                    </style>
                    <div class="table-wrapper">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle sticky-col first-col">No</th>
                                    <th rowspan="2" class="text-center align-middle sticky-col second-col">Nama Siswa</th>
                                    <th rowspan="2" class="text-center align-middle sticky-col third-col">Kelas</th>
                                    @foreach($course->meetings as $meeting)
                                    <th colspan="4" class="text-center">
                                        Pertemuan {{ $loop->iteration }}
                                        <div class="small text-muted">{{ $meeting->tanggal ? $meeting->tanggal->format('d/m/Y') : '-' }}</div>
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
                                @foreach($course->participants as $index => $participant)
                                <tr>
                                    <td class="text-center sticky-col first-col">{{ $index + 1 }}</td>
                                    <td class="sticky-col second-col">{{ $participant->name }}</td>
                                    <td class="text-center sticky-col third-col">
                                        {{ $participant->kelas ? $participant->kelas->nama_kelas : '-' }}
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
                    <div class="save-button-container">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Nilai
                        </button>
                    </div>
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