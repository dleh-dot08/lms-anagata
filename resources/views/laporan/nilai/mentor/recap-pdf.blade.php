<!DOCTYPE html>
<html>
<head>
    <title>Laporan Nilai {{ $course->nama_kelas }}</title>
    <style>
        /* Mengatur orientasi halaman ke landscape (untuk DOMPDF) */
        @page {
            size: landscape;
        }

        body {
            font-family: sans-serif;
            font-size: 8.5px; /* Ukuran font lebih kecil lagi untuk muat lebih banyak data */
            margin: 20px; /* Margin halaman */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px; /* Padding lebih kecil lagi */
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
        }
        .text-left {
            text-align: left;
        }
        /* Style untuk baris rata-rata keseluruhan kelas */
        .class-average-row td {
            font-weight: bold;
            background-color: #e6e6e6; /* Warna latar belakang untuk rata-rata kelas */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rekap Nilai</h1>
        <p>Kelas: {{ $course->nama_kelas }}</p>
        <p>Mentor: {{ $course->mentor->name ?? 'N/A' }}</p>
    </div>

    @if ($enrollments->isEmpty())
        <p>Tidak ada data siswa untuk kelas ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2" class="text-left">Nama Siswa</th>
                    @foreach ($meetings as $meeting)
                        <th colspan="4">Pertemuan {{ $meeting->pertemuan }}</th> {{-- colspan 4 untuk C, D, P, Rata-rata per pertemuan --}}
                    @endforeach
                    <th rowspan="2">Rata-rata<br>Keseluruhan Siswa</th>
                </tr>
                <tr>
                    @foreach ($meetings as $meeting)
                        <th>C</th>
                        <th>D</th>
                        <th>P</th>
                        <th>Avg</th> {{-- Kolom baru untuk rata-rata per pertemuan --}}
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($enrollments as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $student->name }}</td>
                        @php
                            $studentOverallScores = []; // Mengumpulkan semua skor C,D,P dari siswa ini untuk rata-rata keseluruhan siswa
                        @endphp
                        @foreach ($meetings as $meeting)
                            @php
                                $scoreKey = $student->id . '-' . $meeting->id;
                                $score = $scoresData->has($scoreKey) ? $scoresData[$scoreKey]->first() : null;

                                $creativity = $score ? $score->creativity_score : '-';
                                $design = $score ? $score->design_score : '-';
                                $program = $score ? $score->program_score : '-';

                                // Mengumpulkan skor untuk rata-rata per pertemuan
                                $meetingScoresForAvg = [];
                                if (is_numeric($creativity)) $meetingScoresForAvg[] = $creativity;
                                if (is_numeric($design)) $meetingScoresForAvg[] = $design;
                                if (is_numeric($program)) $meetingScoresForAvg[] = $program;

                                // Tambahkan skor ke array keseluruhan siswa
                                if (is_numeric($creativity)) $studentOverallScores[] = $creativity;
                                if (is_numeric($design)) $studentOverallScores[] = $design;
                                if (is_numeric($program)) $studentOverallScores[] = $program;
                            @endphp
                            <td>{{ $creativity }}</td>
                            <td>{{ $design }}</td>
                            <td>{{ $program }}</td>
                            <td>
                                @if (!empty($meetingScoresForAvg))
                                    {{ number_format(array_sum($meetingScoresForAvg) / count($meetingScoresForAvg), 1) }}
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                        <td>
                            @if (!empty($studentOverallScores))
                                {{ number_format(array_sum($studentOverallScores) / count($studentOverallScores), 1) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                {{-- Baris Rata-rata Keseluruhan Kelas Per Pertemuan --}}
                <tr class="class-average-row">
                    <td colspan="2" class="text-left">Rata-rata Kelas Per Pertemuan</td>
                    @php
                        $classOverallScores = []; // Mengumpulkan semua skor C,D,P dari semua siswa di kelas ini untuk rata-rata keseluruhan kelas
                    @endphp
                    @foreach ($meetings as $meeting)
                        @php
                            $meetingCreativityScores = [];
                            $meetingDesignScores = [];
                            $meetingProgramScores = [];
                            $meetingAllScoresPerClass = []; // Semua skor C,D,P untuk pertemuan ini, dari semua siswa

                            foreach ($enrollments as $student) {
                                $scoreKey = $student->id . '-' . $meeting->id;
                                $score = $scoresData->has($scoreKey) ? $scoresData[$scoreKey]->first() : null;

                                if ($score && is_numeric($score->creativity_score)) {
                                    $meetingCreativityScores[] = $score->creativity_score;
                                    $meetingAllScoresPerClass[] = $score->creativity_score; // Untuk rata-rata total pertemuan
                                    $classOverallScores[] = $score->creativity_score; // Untuk rata-rata keseluruhan kelas
                                }
                                if ($score && is_numeric($score->design_score)) {
                                    $meetingDesignScores[] = $score->design_score;
                                    $meetingAllScoresPerClass[] = $score->design_score;
                                    $classOverallScores[] = $score->design_score;
                                }
                                if ($score && is_numeric($score->program_score)) {
                                    $meetingProgramScores[] = $score->program_score;
                                    $meetingAllScoresPerClass[] = $score->program_score;
                                    $classOverallScores[] = $score->program_score;
                                }
                            }
                        @endphp
                        <td>{{ !empty($meetingCreativityScores) ? number_format(array_sum($meetingCreativityScores) / count($meetingCreativityScores), 1) : '-' }}</td>
                        <td>{{ !empty($meetingDesignScores) ? number_format(array_sum($meetingDesignScores) / count($meetingDesignScores), 1) : '-' }}</td>
                        <td>{{ !empty($meetingProgramScores) ? number_format(array_sum($meetingProgramScores) / count($meetingProgramScores), 1) : '-' }}</td>
                        <td>
                            @if (!empty($meetingAllScoresPerClass))
                                {{ number_format(array_sum($meetingAllScoresPerClass) / count($meetingAllScoresPerClass), 1) }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    {{-- Rata-rata Keseluruhan Seluruh Kelas --}}
                    <td>
                        @if (!empty($classOverallScores))
                            {{ number_format(array_sum($classOverallScores) / count($classOverallScores), 1) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>