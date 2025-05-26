@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h4>Rekap Nilai</h4>
    <p class="text-muted">Kelas: <strong>{{ $course->nama_kelas }}</strong></p>

    <a href="{{ route('mentor.scores.export.excel', $course->id) }}" class="btn btn-success btn-sm mb-3">Export Excel</a>
    <a href="{{ route('mentor.scores.export.pdf', $course->id) }}" class="btn btn-danger btn-sm mb-3">Export PDF</a>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th rowspan="2">Nama Peserta</th>
                    @foreach($meetings as $meeting)
                        <th colspan="4">P{{ $meeting->pertemuan }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($meetings as $meeting)
                        <th>Creativity</th>
                        <th>Design</th>
                        <th>Programming</th>
                        <th>Rata-rata</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->user->name }}</td>
                        @foreach($meetings as $meeting)
                            @php
                                $score = $scores[$enrollment->user_id . '-' . $meeting->id][0] ?? null;
                                $avg = $score ? round(($score->creativity_score + $score->design_score + $score->program_score)/3, 2) : '';
                            @endphp
                            <td>{{ $score->creativity_score ?? '-' }}</td>
                            <td>{{ $score->design_score ?? '-' }}</td>
                            <td>{{ $score->program_score ?? '-' }}</td>
                            <td>{{ $avg }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
