<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 0; }
    html, body { margin:0; padding:0; }

    @font-face{
      font-family: "Montserrat";
      font-weight: 400;
      src: url("{{ storage_path('fonts/Montserrat-Regular.ttf') }}") format("truetype");
    }
    @font-face{
      font-family: "Montserrat";
      font-weight: 700;
      src: url("{{ storage_path('fonts/Montserrat-Bold.ttf') }}") format("truetype");
    }

    .page{
      position: relative;
      width: 210mm;
      height: 297mm;
      overflow: hidden;
      font-family: Montserrat, DejaVu Sans, sans-serif;
      font-size: 14px;
      color:#111;
    }

    .bg{
      position:absolute;
      inset:0;
      width:100%;
      height:100%;
      z-index:0;
    }

    /* ✅ area kanan (tabel) — menumpang area kosong page-003 */
    .content{
      position:absolute;
      z-index:2;
      left: 58mm;
      top: 12mm;     /* ✅ dinaikkan agar catatan tidak kepotong */
      width: 140mm;
    }

    table{
      width:100%;
      border-collapse: collapse;
      table-layout: fixed;
    }
    th, td{
      border: 1.2px solid #111;
      padding: 4mm 4mm;
      vertical-align: middle;
      word-wrap: break-word;
    }
    th{
      font-weight: 700;
      text-align: center;
    }

    .lbl{
      font-weight: 700;
      width: 35%;
    }
    .val{
      font-weight: 700;
    }

    .center{ text-align:center; }
    .left{ text-align:left; }
    .box-title{
      font-weight: 700;
      text-align:center;
    }

    /* ✅ QR: posisikan ke kotak sidebar */
    .qrwrap{
      position:absolute;
      z-index:2;
      left: 12mm;    /* ✅ adjust kiri/kanan */
      top: 170mm;    /* ✅ adjust naik/turun */
      width: 36mm;
      height: 36mm;
      display:flex;
      align-items:center;
      justify-content:center;
      border: 1.2px solid #111;
      background:#fff;
    }
    .qrwrap img{
      width: 32mm;
      height: 32mm;
      object-fit: contain;
      display:block;
    }

    /* QR FIXED POSITION */
    .qrbox{
    position:absolute;
    z-index:10;

    /* ⬇️ INI YANG DISESUAIKAN */
    left: 17mm;     /* jarak dari kiri halaman */
    top: 185mm;     /* jarak dari atas halaman */

    width: 26mm;    /* ukuran kotak QR */
    height: 26mm;

    display:flex;
    align-items:center;
    justify-content:center;

    background:#fff;
    }

    .qrbox img{
    width:100%;
    height:100%;
    object-fit:contain;
    }

    /* ✅ Catatan: aman dari kepotong */
    .note{
      margin-top: 6mm;
      border: 1.2px solid #111;
    }
    .note .head{
      display:inline-block;
      padding: 2mm 4mm;
      border-right: 1.2px solid #111;
      border-bottom: 1.2px solid #111;
      font-weight: 700;
      background:#fff;
    }
    .note .body{
      padding: 4mm 4mm;
      min-height: 16mm;
      max-height: 24mm;
      overflow: hidden;
      font-weight: 700;
      text-transform: uppercase;
      line-height: 1.2;
    }
  </style>
</head>
<body>
@php
  $nama     = data_get($payload,'student.name','-');
  $kelas    = data_get($payload,'student.kelas_label','-');
  $semester = data_get($payload,'semester.label','-');
  $sekolah  = data_get($payload,'school.name','-');
  $program  = data_get($payload,'course.title','-');

  $hadir = data_get($payload,'attendance.summary.hadir','-');
  $sakit = data_get($payload,'attendance.summary.sakit','-');
  $izin  = data_get($payload,'attendance.summary.izin','-');
  $alpha = data_get($payload,'attendance.summary.alpha','-');

  $platform   = data_get($payload,'course.platform','-');
  $kategori   = data_get($payload,'course.category','-');
  $avgProject = data_get($payload,'scores.avg_project','-');
  $logicCt    = data_get($payload,'scores.logic_ct','-');
  $creativity = data_get($payload,'scores.creativity','-');

  $note = (string) data_get($payload,'mentor_note.note','-');
  $noteUpper = mb_strtoupper($note);

  // ✅ auto-shrink catatan kalau panjang (biar muat box, tidak kepotong)
  $len = mb_strlen($noteUpper);
  $noteFont = $len > 140 ? 11.5 : ($len > 95 ? 12.5 : 14);
@endphp

<div class="page">
  <img class="bg" src="{{ $backgroundDataUri }}" alt="bg">

  {{-- QR di sidebar --}}
    @if(!empty($qrDataUri))
        <div class="qrbox">
            <img src="{{ $qrDataUri }}" alt="QR Verify">
        </div>
    @endif

    <div class="content">

    {{-- DATA SISWA --}}
    <table>
      <tr>
        <th colspan="3" class="box-title">DATA SISWA</th>
      </tr>
      <tr>
        <td class="lbl">NAMA</td>
        <td colspan="2" class="val left">{{ $nama }}</td>
      </tr>
      <tr>
        <td class="lbl">KELAS</td>
        <td class="val center">{{ $kelas }}</td>
        <td class="val center">{{ $semester }}</td>
      </tr>
      <tr>
        <td class="lbl">SEKOLAH</td>
        <td colspan="2" class="val left">{{ $sekolah }}</td>
      </tr>
      <tr>
        <td class="lbl">PROGRAM</td>
        <td colspan="2" class="val left">{{ $program }}</td>
      </tr>
    </table>

    {{-- spacer --}}
    <div style="height:6mm"></div>

    {{-- KEHADIRAN --}}
    <table class="center">
      <tr>
        <th>HADIR</th>
        <th>SAKIT</th>
        <th>IZIN</th>
        <th>ALPHA</th>
      </tr>
      <tr>
        <td class="val">{{ $hadir }}</td>
        <td class="val">{{ $sakit }}</td>
        <td class="val">{{ $izin }}</td>
        <td class="val">{{ $alpha }}</td>
      </tr>
    </table>

    {{-- spacer --}}
    <div style="height:6mm"></div>

    {{-- DETAIL PEMBELAJARAN --}}
    <table>
      <tr>
        <th colspan="2" class="box-title">DETAIL PEMBELAJARAN</th>
      </tr>
      <tr>
        <td class="lbl">PLATFORM</td>
        <td class="val center">{{ $platform }}</td>
      </tr>
      <tr>
        <td class="lbl">KATEGORI</td>
        <td class="val center">{{ $kategori }}</td>
      </tr>
      <tr>
        <td class="lbl">NILAI RATA-RATA PROYEK DIGITAL</td>
        <td class="val center">{{ $avgProject }}</td>
      </tr>
      <tr>
        <td class="lbl">PEMAHAMAN LOGIKA ALGORITMA DAN COMPUTATIONAL THINKING</td>
        <td class="val center">{{ $logicCt }}</td>
      </tr>
      <tr>
        <td class="lbl">KREATIVITAS</td>
        <td class="val center">{{ $creativity }}</td>
      </tr>
    </table>

    {{-- CATATAN --}}
    <div class="note">
      <div class="head">CATATAN MENTOR</div>
      <div class="body" style="font-size: {{ $noteFont }}px;">
        {{ $noteUpper }}
      </div>
    </div>

  </div>
</div>

</body>
</html>
