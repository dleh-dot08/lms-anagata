@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h4 class="mb-2">Input Nilai</h4>
    <p class="text-muted mb-3">
        Kelas: <strong>{{ $course->nama_kelas }}</strong><br>
        Pertemuan ke-{{ $meeting->pertemuan }} — {{ $meeting->judul }}
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('mentor.scores.store', [$course->id, $meeting->id]) }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Creativity<br><small>(0-100)</small></th>
                        <th>Design<br><small>(0-100)</small></th>
                        <th>Programming<br><small>(0-100)</small></th>
                        <th>Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $index => $enrollment)
                    @php
                        $user = $enrollment->user;
                        $score = $existingScores[$user->id] ?? null;
                        $avg = $score ? round(($score->creativity_score + $score->design_score + $score->program_score) / 3, 2) : '';
                    @endphp
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>
                            <input type="number"
                                   name="scores[{{ $index }}][creativity_score]"
                                   class="form-control text-center score-input"
                                   min="0" max="100" required
                                   value="{{ $score->creativity_score ?? '' }}">
                        </td>
                        <td>
                            <input type="number"
                                   name="scores[{{ $index }}][design_score]"
                                   class="form-control text-center score-input"
                                   min="0" max="100" required
                                   value="{{ $score->design_score ?? '' }}">
                        </td>
                        <td>
                            <input type="number"
                                   name="scores[{{ $index }}][program_score]"
                                   class="form-control text-center score-input"
                                   min="0" max="100" required
                                   value="{{ $score->program_score ?? '' }}">
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control text-center avg-field"
                                   readonly placeholder="Auto"
                                   value="{{ $avg }}">
                        </td>
                        <input type="hidden" name="scores[{{ $index }}][peserta_id]" value="{{ $user->id }}">
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('mentor.scores.index', $course->id) }}" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-success">Simpan Nilai</button>
        </div>
    </form>
</div>

<script>
    function updateAverages() {
        document.querySelectorAll('tr').forEach(row => {
            const c = parseFloat(row.querySelector('input[name*="[creativity_score]"]')?.value) || 0;
            const d = parseFloat(row.querySelector('input[name*="[design_score]"]')?.value) || 0;
            const p = parseFloat(row.querySelector('input[name*="[program_score]"]')?.value) || 0;
            const avg = ((c + d + p) / 3).toFixed(2);
            const avgInput = row.querySelector('.avg-field');
            if (avgInput) avgInput.value = avg;
        });
    }

    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('input', updateAverages);
    });

    window.addEventListener('DOMContentLoaded', updateAverages);
</script>
@endsection
