<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Nilai {{ $course->nama_kelas }}</title>
</head>
<body>
    <h3>Rekap Nilai - {{ $course->nama_kelas }}</h3>
    
    <table border="1">
        <thead>
            <tr>
                <th rowspan="2">Nama Peserta</th>
                @foreach($meetings as $meeting)
                    <th colspan="4">Pertemuan {{ $meeting->pertemuan }}</th>
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
</body>
</html>