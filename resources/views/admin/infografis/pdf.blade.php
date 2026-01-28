<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Infografis PDF</title>
  <style>
    @page { margin: 18px 18px 22px 18px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; }
    .muted { color:#6b7280; }
    .title { font-size: 16px; font-weight: 700; margin: 0 0 2px 0; }
    .subtitle { font-size: 11px; margin: 0 0 10px 0; }
    .badge { display:inline-block; padding:3px 8px; border-radius:999px; font-size:10px; border:1px solid #ddd; }
    .badge-ok { background:#e8fff3; border-color:#b7f7d2; }
    .badge-warn { background:#fff7e6; border-color:#ffe1a6; }
    .card { border:1px solid #e5e7eb; border-radius:10px; padding:10px; margin-bottom:10px; }
    .section-title { font-weight:700; margin:0 0 6px 0; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #e5e7eb; padding:6px 6px; vertical-align: top; }
    th { background:#f3f4f6; text-align:left; font-size:10px; }
    .small { font-size:10px; }
    .right { text-align:right; }
    .center { text-align:center; }
    .mb6 { margin-bottom:6px; }
    .mb10 { margin-bottom:10px; }
    .nowrap { white-space:nowrap; }
    .pill { display:inline-block; padding:2px 8px; border:1px solid #e5e7eb; border-radius:999px; font-size:10px; background:#fafafa; margin-right:6px; margin-bottom:6px; }
    .row { display:flex; gap:10px; }
    .col { flex:1; }
    .table-fit { table-layout: fixed; }
    .w-no { width: 5%; }
    .w-name { width: 50%; }
    .w-kelas { width: 15%; }
    .w-hadir { width: 10%; }
    .w-nilai { width: 10%; }
    .w-skala { width: 10%; }
    .chart-img { width: 100%; height: auto; display:block; }
  </style>
</head>
<body>

@php
  $n0 = fn($x) => is_numeric($x) ? number_format((float)$x, 0, ',', '.') : ($x ?? 0);

  // data aman
  $cardsArr = is_array($cards ?? null) ? $cards : [];
  $kehad = $kehadiran ?? ['total'=>0,'hadir'=>0,'tidak_hadir'=>0,'izin'=>0,'sakit'=>0,'rate_hadir'=>0];

  $trend = is_array($trendCombined ?? null) ? $trendCombined : [];
  if (empty($trend)) {
    $trend = [[ 'x'=>'N/A', 'avg_score'=>0, 'hadir'=>0, 'izin'=>0, 'sakit'=>0, 'tidak_hadir'=>0 ]];
  }

  $pcol = collect($participants ?? []);

  // === INFO DATA (sesuai request) ===
  $semesterName = $selectedSemester->name ?? ('Semester#'.$semesterId);
  $sekolahName  = $selectedSchoolName ?? 'ALL';
  $courseName   = $selectedCourseName ?? 'ALL (Agregat)';
  $durasi = ($selectedSemester->start_date ?? '-') . ' s/d ' . ($selectedSemester->end_date ?? '-');

  // Mentor
  $mentorText = '-';
  if (!empty($courseMeta) && !empty($courseMeta['mentor_names'])) {
    $mentorText = implode(', ', $courseMeta['mentor_names']);
  }

  // jumlah pertemuan & peserta aktif dari cards (label harus match)
  $jumlahPertemuan = (int)(collect($cardsArr)->firstWhere('label','Jumlah Pertemuan')['value'] ?? 0);
  $pesertaAktif    = (int)(collect($cardsArr)->firstWhere('label','Peserta Aktif')['value'] ?? 0);

  // === Komposisi Absensi (table) ===
  $totalAtt = max(1, (int)($kehad['total'] ?? 0));
  $absCounts = [
    'Hadir' => (int)($kehad['hadir'] ?? 0),
    'Tidak Hadir' => (int)($kehad['tidak_hadir'] ?? 0),
    'Izin' => (int)($kehad['izin'] ?? 0),
    'Sakit' => (int)($kehad['sakit'] ?? 0),
  ];
  $absPct = [
    'Hadir' => round(($absCounts['Hadir']/$totalAtt)*100, 1),
    'Tidak Hadir' => round(($absCounts['Tidak Hadir']/$totalAtt)*100, 1),
    'Izin' => round(($absCounts['Izin']/$totalAtt)*100, 1),
    'Sakit' => round(($absCounts['Sakit']/$totalAtt)*100, 1),
  ];

  // === Komposisi Nilai (table) ===
  $skalaCounts = [
    'Excellent (≥95)' => 0,
    'Very Good (90–94.9)' => 0,
    'Good (80–89.9)' => 0,
    'Average (<80)' => 0,
  ];
  foreach ($pcol as $pp) {
    $avg = (float)($pp->avg_score ?? 0);
    if ($avg >= 95) $skalaCounts['Excellent (≥95)']++;
    elseif ($avg >= 90) $skalaCounts['Very Good (90–94.9)']++;
    elseif ($avg >= 80) $skalaCounts['Good (80–89.9)']++;
    else $skalaCounts['Average (<80)']++;
  }
  $pCount = max(1, $pcol->count());
  $skalaPct = [];
  foreach ($skalaCounts as $k=>$v) {
    $skalaPct[$k] = round(($v/$pCount)*100, 1);
  }

  // === LINE CHART (SVG => base64 IMG) ===
  $labels = array_map(fn($r) => (string)($r['x'] ?? ''), $trend);
  $avgSeries = array_map(fn($r) => (float)($r['avg_score'] ?? 0), $trend);

  $pctSeries = array_map(function($r){
    $hadir = (int)($r['hadir'] ?? 0);
    $izin  = (int)($r['izin'] ?? 0);
    $sakit = (int)($r['sakit'] ?? 0);
    $tdk   = (int)($r['tidak_hadir'] ?? 0);
    $tot = $hadir + $izin + $sakit + $tdk;
    return $tot > 0 ? round(($hadir/$tot)*100, 1) : 0;
  }, $trend);

  $W = 520; $H = 170; $padL = 34; $padR = 14; $padT = 14; $padB = 26;
  $innerW = $W - $padL - $padR;
  $innerH = $H - $padT - $padB;

  $maxScore = max(10, ceil(max($avgSeries) / 10) * 10);
  $count = max(1, count($avgSeries));
  $stepX = $count > 1 ? ($innerW / ($count - 1)) : 0;

  $ptsScore = [];
  $ptsPct = [];

  for ($i=0; $i<$count; $i++) {
    $x = $padL + ($stepX * $i);
    $yScore = $padT + ($innerH * (1 - ($avgSeries[$i] / $maxScore)));
    $yPct   = $padT + ($innerH * (1 - ($pctSeries[$i] / 100)));
    $ptsScore[] = round($x,1).','.round($yScore,1);
    $ptsPct[]   = round($x,1).','.round($yPct,1);
  }

  $xEvery = ($count <= 8) ? 1 : (int)ceil($count/8);

  // Build SVG as string (important: add xmlns)
  $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$W.'" height="'.$H.'" viewBox="0 0 '.$W.' '.$H.'">';

  // grid
  for ($g=0; $g<=5; $g++) {
    $gy = $padT + ($innerH * ($g/5));
    $svg .= '<line x1="'.$padL.'" y1="'.$gy.'" x2="'.($W-$padR).'" y2="'.$gy.'" stroke="#e5e7eb" stroke-width="1"/>';
  }

  // y labels
  for ($g=0; $g<=5; $g++) {
    $val = (int)round($maxScore * (1-($g/5)));
    $gy = $padT + ($innerH * ($g/5));
    $svg .= '<text x="2" y="'.($gy+4).'" font-size="9" fill="#6b7280">'.$val.'</text>';
  }

  // polylines
  $svg .= '<polyline points="'.implode(' ', $ptsScore).'" fill="none" stroke="#111827" stroke-width="2"/>';
  $svg .= '<polyline points="'.implode(' ', $ptsPct).'" fill="none" stroke="#9ca3af" stroke-width="2" stroke-dasharray="4 3"/>';

  // points + labels
  for ($i=0; $i<$count; $i++) {
    $x = $padL + ($stepX * $i);
    $yS = $padT + ($innerH * (1 - ($avgSeries[$i] / $maxScore)));
    $yP = $padT + ($innerH * (1 - ($pctSeries[$i] / 100)));
    $svg .= '<circle cx="'.$x.'" cy="'.$yS.'" r="2.5" fill="#111827"/>';
    $svg .= '<text x="'.$x.'" y="'.($yS-6).'" text-anchor="middle" font-size="9" fill="#111827">'.round($avgSeries[$i],1).'</text>';
    $svg .= '<circle cx="'.$x.'" cy="'.$yP.'" r="2.5" fill="#9ca3af"/>';
    $svg .= '<text x="'.$x.'" y="'.($yP-6).'" text-anchor="middle" font-size="9" fill="#6b7280">'.round($pctSeries[$i],1).'%</text>';
  }

  // x labels
  for ($i=0; $i<$count; $i++) {
    if ($i % $xEvery === 0) {
      $x = $padL + ($stepX * $i);
      $lab = htmlspecialchars($labels[$i] ?? '', ENT_QUOTES, 'UTF-8');
      $svg .= '<text x="'.$x.'" y="'.($H-8).'" text-anchor="middle" font-size="9" fill="#6b7280">'.$lab.'</text>';
    }
  }

  // legend
  $svg .= '<rect x="'.$padL.'" y="6" width="10" height="3" fill="#111827"/>';
  $svg .= '<text x="'.($padL+14).'" y="10" font-size="9" fill="#111827">Avg Nilai</text>';
  $svg .= '<rect x="'.($padL+90).'" y="6" width="10" height="3" fill="#9ca3af"/>';
  $svg .= '<text x="'.($padL+104).'" y="10" font-size="9" fill="#6b7280">% Hadir</text>';

  $svg .= '</svg>';

  // Base64 encode for DomPDF
  $chartImgSrc = 'data:image/svg+xml;base64,'.base64_encode($svg);
@endphp

{{-- Header --}}
<div class="mb10">
  <div class="title">Infografis Dashboard</div>
  <div class="subtitle muted">
    Export berdasarkan filter admin.
  </div>

  @if(!empty($summaryApprovedAt))
    <span class="badge badge-ok">APPROVED</span>
  @else
    <span class="badge badge-warn">DRAFT</span>
  @endif
</div>

{{-- 1) INFO DATA (sesuai request) --}}
<div class="card mb10">
  <div class="section-title">Informasi Data</div>
  <div class="small">
    <div class="pill">Semester: <b>{{ $semesterName }}</b></div>
    <div class="pill">Sekolah: <b>{{ $sekolahName }}</b></div>
    <div class="pill">Kursus: <b>{{ $courseName }}</b></div>
    <div class="pill">Durasi: <b>{{ $durasi }}</b></div>
    <div class="pill">Mentor: <b>{{ $mentorText }}</b></div>
    <div class="pill">Jumlah Pertemuan: <b>{{ $n0($jumlahPertemuan) }}</b></div>
    <div class="pill">Peserta Aktif: <b>{{ $n0($pesertaAktif) }}</b></div>
  </div>
</div>

{{-- 2) Kotak data nilai DIHAPUS: tidak ada KPI nilai --}}

{{-- 3) Diagram Tren (pakai IMG base64 SVG biar DomPDF render) --}}
<div class="card mb10">
  <div class="section-title">Diagram Garis Tren</div>
  <div class="small muted mb6">X = tanggal/pertemuan (sesuai filter) • menampilkan Avg Nilai & % Hadir</div>

  <img class="chart-img" src="{{ $chartImgSrc }}" alt="Chart Tren"/>
</div>

{{-- 4) Komposisi Absensi (table seperti komposisi nilai) --}}
<div class="row mb10">
  @php
    // Ambil trend rows (x = pertemuan / tanggal)
    $trendRows = collect($trend ?? $trendCombined ?? []);

    // Normalisasi jadi array aman
    $trendRows = $trendRows->map(function($r){
        if (is_object($r)) $r = (array)$r;

        $hadir = (int)($r['hadir'] ?? 0);
        $tdk   = (int)($r['tidak_hadir'] ?? 0);
        $izin  = (int)($r['izin'] ?? 0);
        $sakit = (int)($r['sakit'] ?? 0);

        $tot = max(1, $hadir + $tdk + $izin + $sakit);

        return [
        'x' => (string)($r['x'] ?? ''),
        'pct_hadir' => round(($hadir/$tot)*100, 1),
        'pct_tidak' => round(($tdk/$tot)*100, 1),
        'pct_izin'  => round(($izin/$tot)*100, 1),
        'pct_sakit' => round(($sakit/$tot)*100, 1),
        ];
    });

    // Header kolom (1..n atau tanggal)
    $xHeaders = $trendRows->pluck('x')->values()->all();

    // helper buat average
    $avgOf = function($key) use ($trendRows) {
        return $trendRows->count() ? round($trendRows->avg($key), 1) : 0;
    };

    // rows pivot
    $absPivot = [
        [
        'no' => 1,
        'label' => 'Hadir',
        'key' => 'pct_hadir',
        'avg' => $avgOf('pct_hadir'),
        ],
        [
        'no' => 2,
        'label' => 'Tidak Hadir',
        'key' => 'pct_tidak',
        'avg' => $avgOf('pct_tidak'),
        ],
        [
        'no' => 3,
        'label' => 'Izin',
        'key' => 'pct_izin',
        'avg' => $avgOf('pct_izin'),
        ],
        [
        'no' => 4,
        'label' => 'Sakit',
        'key' => 'pct_sakit',
        'avg' => $avgOf('pct_sakit'),
        ],
    ];
    @endphp

    <div class="col card">
    <div class="section-title">Komposisi Absensi</div>
    <div class="small muted mb6">
        Pivot per pertemuan/tanggal. Kolom terakhir adalah rata-rata persentase (Average) per status.
    </div>

    <table class="small" style="table-layout:fixed;">
        <thead>
        <tr>
            <th class="center" style="width:34px;">No</th>
            <th style="width:120px;">Status Absensi</th>

            {{-- kolom pertemuan/tanggal --}}
            @foreach($xHeaders as $xh)
            <th class="center" style="width:46px;">{{ $xh }}</th>
            @endforeach

            {{-- kolom average --}}
            <th class="center" style="width:110px;">Persentase (Avg)</th>
        </tr>
        </thead>

        <tbody>
        @if($trendRows->isEmpty())
            <tr>
            <td colspan="{{ 3 + count($xHeaders) }}" class="muted">Tidak ada data absensi per pertemuan.</td>
            </tr>
        @else
            @foreach($absPivot as $row)
            <tr>
                <td class="center">{{ $row['no'] }}</td>
                <td><b>{{ $row['label'] }}</b></td>

                @foreach($trendRows as $t)
                <td class="center">{{ $t[$row['key']] }}%</td>
                @endforeach

                <td class="center"><b>{{ $row['avg'] }}%</b></td>
            </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <div class="small muted" style="margin-top:6px;">
        Catatan: persen dihitung dari total status per pertemuan (Hadir+Tidak+Izin+Sakit) agar konsisten.
    </div>
    </div>

  <div class="col card">
    <div class="section-title">Komposisi Nilai</div>
    <div class="small muted mb6">Distribusi skala nilai berdasarkan rata-rata nilai peserta.</div>

    <table class="small">
      <thead>
        <tr>
          <th>Skala</th>
          <th class="center" style="width:90px;">Jumlah</th>
          <th class="center" style="width:90px;">Persen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($skalaCounts as $k => $v)
          <tr>
            <td>{{ $k }}</td>
            <td class="center">{{ $v }}</td>
            <td class="center">{{ $skalaPct[$k] ?? 0 }}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Resume --}}
<div class="card mb10">
  <div class="section-title">Kesimpulan (Resume Admin)</div>
  @if(isset($summaryText) && trim((string)$summaryText) !== '')
    <div class="small">{!! nl2br(e($summaryText)) !!}</div>
  @else
    <div class="small muted">Belum ada resume untuk kombinasi ini.</div>
  @endif
</div>

{{-- Participants table (fit, nama lebih lebar) --}}
<div class="card">
  <div class="section-title">Daftar Peserta</div>
  <div class="small muted mb6">Kolom Nama dibuat lebih lebar agar tidak kepotong.</div>

  <table class="small table-fit">
    <thead>
      <tr>
        <th class="w-no center">No</th>
        <th class="w-name">Nama</th>
        <th class="w-kelas">Kelas</th>
        <th class="w-hadir center">% Hadir</th>
        <th class="w-nilai right">Avg Nilai</th>
        <th class="w-skala">Skala</th>
      </tr>
    </thead>
    <tbody>
      @php $no=1; @endphp
      @forelse(($participants ?? []) as $p)
        @php
          $hadir = (int)($p->hadir ?? 0);
          $tdk   = (int)($p->tidak_hadir ?? 0);
          $izin  = (int)($p->izin ?? 0);
          $sakit = (int)($p->sakit ?? 0);
          $den = $hadir + $tdk + $izin + $sakit;
          $persen = $den > 0 ? round(($hadir/$den)*100, 1) : 0;

          $avg = (float)($p->avg_score ?? 0);
          if ($avg >= 95) $skala = 'Excellent';
          elseif ($avg >= 90) $skala = 'Very Good';
          elseif ($avg >= 80) $skala = 'Good';
          else $skala = 'Average';
        @endphp
        <tr>
          <td class="center">{{ $no++ }}</td>
          <td><b>{{ $p->name }}</b></td>
          <td>{{ $p->kelas_name ?? '-' }}</td>
          <td class="center">{{ $persen }}%</td>
          <td class="right"><b>{{ $avg }}</b></td>
          <td>{{ $skala }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="muted">Tidak ada data peserta.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

</body>
</html>
