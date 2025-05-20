<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Nilai {{ $course->nama_kelas }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h3 {
            margin-bottom: 5px;
        }
        .header p {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="header">
        <h3>REKAPITULASI NILAI PESERTA</h3>
        <p>Kelas: {{ $course->nama_kelas }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($course->waktu_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($course->waktu_akhir)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 20%;">Nama Peserta</th>
                @foreach($meetings as $meeting)
                    <th colspan="4">Pertemuan {{ $meeting->pertemuan }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($meetings as $meeting)
                    <th style="width: 12%;">Creativity</th>
                    <th style="width: 12%;">Design</th>
                    <th style="width: 12%;">Programming</th>
                    <th style="width: 12%;">Rata-rata</th>
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
                            $avg = $score ? round(($score->creativity_score + $score->design_score + $score->program_score)/3, 2) : '-';
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

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>