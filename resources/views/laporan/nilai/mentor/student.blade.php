@extends('layouts.mentor.template')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Rapor: {{ $student->name }}</h2>

    <p><strong>Rata-rata Nilai:</strong> {{ number_format($avg, 2) }}</p>

    <table class="w-full table-auto border my-4">
        <thead class="bg-gray-100">
            <tr>
                <th>Pertemuan</th>
                <th>Creativity</th>
                <th>Program</th>
                <th>Design</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($student->scores as $score)
            <tr>
                <td>{{ $score->meeting->title ?? '-' }}</td>
                <td>{{ $score->creativity_score }}</td>
                <td>{{ $score->program_score }}</td>
                <td>{{ $score->design_score }}</td>
                <td>{{ $score->total_score }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <canvas id="progressChart" class="my-6"></canvas>

    <a href="{{ route('mentor.score.exportPdf', $student->id) }}" class="bg-red-500 text-white px-4 py-2 rounded">Download PDF</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('progressChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($student->scores->pluck('meeting.title')) !!},
            datasets: [{
                label: 'Total Score',
                data: {!! json_encode($student->scores->pluck('total_score')) !!},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.2,
                fill: false
            }]
        },
    });
</script>
@endsection
