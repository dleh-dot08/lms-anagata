<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Nilai {{ $course->nama_kelas }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
        }
        .page-break {
            page-break-after: always;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        tr {
            page-break-inside: avoid;
        }
        .detail-table {
            margin-bottom: 20px;
        }
        .scores-table {
            font-size: 10px;
            margin-bottom: 20px;
        }
        .text-center {
            text-align: center;
        }
        .meeting-date {
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Detail Kursus Table -->
    <table class="detail-table">
        <tr>
            <td colspan="2"><strong>Detail Kursus</strong></td>
        </tr>
        <tr>
            <td width="20%">Nama Kelas</td>
            <td>{{ $course->nama_kelas }}</td>
        </tr>
        <tr>
            <td>Kode Unik</td>
            <td>{{ $course->kode_unik }}</td>
        </tr>
        <tr>
            <td>Mentor</td>
            <td>{{ $course->mentor->name ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>Sekolah</td>
            <td>{{ $course->sekolah->nama_sekolah ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>{{ $course->kelas->nama_kelas ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>Program</td>
            <td>{{ $course->program->nama_program ?? 'Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>Kategori</td>
            <td>{{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</td>
        </tr>
    </table>

    @php
        $meetings = $course->meetings->chunk(5);
    @endphp

    @foreach($meetings as $meetingChunk)
        <!-- Scores Table with repeating headers -->
        <table class="scores-table">
            <thead>
                <tr>
                    <th rowspan="2" class="text-center" style="width: 3%">No</th>
                    <th rowspan="2" class="text-center" style="width: 15%">Nama Siswa</th>
                    <th rowspan="2" class="text-center" style="width: 7%">Kelas</th>
                    @foreach($meetingChunk as $meeting)
                        <th colspan="4" class="text-center">
                            Pertemuan {{ $loop->parent->index * 5 + $loop->iteration }}
                            <div class="meeting-date">
                                {{ $meeting->tanggal_pelaksanaan ? \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->format('d/m/Y') : '-' }}
                            </div>
                        </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach($meetingChunk as $meeting)
                        <th class="text-center">Creativity</th>
                        <th class="text-center">Program</th>
                        <th class="text-center">Design</th>
                        <th class="text-center">Total</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($course->enrollments ?? $course->participants as $index => $participant)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participant->name ?? $participant->user->name }}</td>
                        <td class="text-center">
                            @if(isset($participant->kelas))
                                {{ $participant->kelas->nama_kelas ?? '-' }}
                            @else
                                {{ $participant->user->kelas->nama_kelas ?? '-' }}
                            @endif
                        </td>
                        @foreach($meetingChunk as $meeting)
                            @php
                                $score = App\Models\Score::where('peserta_id', isset($participant->id) ? $participant->id : $participant->user->id)
                                    ->where('meeting_id', $meeting->id)
                                    ->first();
                            @endphp
                            <td class="text-center">{{ ($score && $score->creativity_score !== null) ? number_format($score->creativity_score, 1) : '-' }}</td>
                            <td class="text-center">{{ ($score && $score->program_score !== null) ? number_format($score->program_score, 1) : '-' }}</td>
                            <td class="text-center">{{ ($score && $score->design_score !== null) ? number_format($score->design_score, 1) : '-' }}</td>
                            <td class="text-center">{{ ($score && $score->total_score !== null) ? number_format($score->total_score, 1) : '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html> 