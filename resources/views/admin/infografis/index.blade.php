@extends('layouts.admin.template')

@section('content')
@php
    // Safe defaults
    $semesterSelected = !empty($semesterId);
    $blockedAllCourseNeedSchool = !empty($mustPickSchoolForAllCourse);

    $cardsSafe = (!empty($cards) && is_array($cards)) ? $cards : [
        ['label' => 'Jumlah Pertemuan', 'value' => 0],
        ['label' => 'Total Siswa', 'value' => 0],
        ['label' => 'Siswa Aktif', 'value' => 0],
    ];

    $kehadiranSafe = $kehadiran ?? [
        'total' => 0,
        'hadir' => 0,
        'tidak_hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'rate_hadir' => 0,
        'total_tidak_hadir' => 0,
    ];

    $trendForJs = (!empty($trendCombined) && is_array($trendCombined) && count($trendCombined))
        ? $trendCombined
        : [[
            'x' => 'N/A',
            'avg_score' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'tidak_hadir' => 0,
        ]];

    $selectedSemesterName = $selectedSemester->name ?? '-';
    $selectedSemesterYear = $selectedSemester->year ?? '-';
    $selectedSchool = $selectedSchoolName ?? 'ALL';
    $selectedCourse = $selectedCourseName ?? 'ALL (Agregat)';

    // ========= Persentase Kehadiran per status (dari summary) =========
    $totalAtt = (int)($kehadiranSafe['total'] ?? 0);
    $hadirCnt = (int)($kehadiranSafe['hadir'] ?? 0);
    $izinCnt  = (int)($kehadiranSafe['izin'] ?? 0);
    $sakitCnt = (int)($kehadiranSafe['sakit'] ?? 0);
    $tdkCnt   = (int)($kehadiranSafe['tidak_hadir'] ?? 0);

    $pctHadir = $totalAtt > 0 ? round(($hadirCnt / $totalAtt) * 100, 1) : 0;
    $pctIzin  = $totalAtt > 0 ? round(($izinCnt  / $totalAtt) * 100, 1) : 0;
    $pctSakit = $totalAtt > 0 ? round(($sakitCnt / $totalAtt) * 100, 1) : 0;
    $pctTdk   = $totalAtt > 0 ? round(($tdkCnt   / $totalAtt) * 100, 1) : 0;

    // ========= Rata-rata Nilai (overall) dari trend avg_score =========
    // (pakai avg_score per titik, abaikan null)
    $avgScores = collect($trendForJs)->pluck('avg_score')->filter(fn($v) => $v !== null)->map(fn($v)=> (float)$v)->values();
    $avgNilaiAll = $avgScores->count() ? round($avgScores->avg(), 1) : 0;
    $minNilaiAll = $avgScores->count() ? round($avgScores->min(), 1) : 0;
    $maxNilaiAll = $avgScores->count() ? round($avgScores->max(), 1) : 0;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ====== HEADER ====== --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex align-items-start align-items-md-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="mb-1">Judul Laporan Infografis</h4>
                <div class="text-muted small">
                    Semester <b>{{ $selectedSemesterName }} ({{ $selectedSemesterYear }})</b>,
                    Sekolah <b>{{ $selectedSchool }}</b>,
                    Kursus <b>{{ $selectedCourse }}</b>.
                </div>
            </div>

            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ url()->current() }}">Reset</a>
                <a class="btn btn-danger {{ !$semesterSelected ? 'disabled' : '' }}"
                   href="{{ !$semesterSelected ? '#' : route('admin.infografis.export', request()->query()) }}">
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ====== FILTER ====== --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.infografis') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Semester</label>
                    <select name="semester_id" class="form-select" required>
                        <option value="">-- Pilih Semester --</option>
                        @foreach(($semesters ?? []) as $s)
                            <option value="{{ $s->id }}" {{ (int)($semesterId ?? 0) === (int)$s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->year }}) {{ $s->is_active ? '• Aktif' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if(!empty($selectedSemester) && (empty($selectedSemester->start_date) || empty($selectedSemester->end_date)))
                        <small class="text-danger">Semester belum punya start_date/end_date.</small>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sekolah</label>
                    <select name="sekolah_id" class="form-select">
                        <option value="">ALL Sekolah</option>
                        @foreach(($schools ?? []) as $sc)
                            <option value="{{ $sc->id }}" {{ (int)($sekolahId ?? 0) === (int)$sc->id ? 'selected' : '' }}>
                                {{ $sc->nama_sekolah }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika Kursus = ALL, sekolah wajib dipilih.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kursus</label>
                    <select name="course_id" class="form-select">
                        <option value="">ALL Kursus (Agregat)</option>
                        @foreach(($courses ?? []) as $c)
                            <option value="{{ $c->id }}" {{ (int)($courseId ?? 0) === (int)$c->id ? 'selected' : '' }}>
                                {{ $c->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika kursus dipilih: X = pertemuan.</small>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary">Terapkan</button>
                </div>
            </form>

            @if(!$semesterSelected)
                <div class="alert alert-info mt-3 mb-0">
                    Silakan pilih <b>Semester</b> dulu.
                </div>
            @elseif($blockedAllCourseNeedSchool)
                <div class="alert alert-warning mt-3 mb-0">
                    Untuk mode <b>ALL Kursus</b>, mohon pilih <b>Sekolah</b>.
                </div>
            @endif
        </div>
    </div>

    {{-- ====== STAT ROW (2 cards) ====== --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ $cardsSafe[0]['label'] ?? 'Statistik 1' }}</div>
                    <div class="display-6 fw-bold mb-0">{{ number_format((float)($cardsSafe[0]['value'] ?? 0)) }}</div>
                    <div class="text-muted small mt-1">Keterangan 1</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-body">
                    <div class="text-muted small mb-1">{{ $cardsSafe[1]['label'] ?? 'Statistik 2' }}</div>
                    <div class="display-6 fw-bold mb-0">{{ number_format((float)($cardsSafe[1]['value'] ?? 0)) }}</div>
                    <div class="text-muted small mt-1">Keterangan 2</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== MAIN GRAPH + INFO DATA ====== --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <div>
                            <div class="fw-semibold">Grafik Utama</div>
                            <div class="text-muted small">
                                @if(!empty($courseId)) X = Pertemuan @else X = Tanggal @endif
                                • Legend: Nilai, Hadir, Tidak Hadir, Izin, Sakit
                            </div>
                        </div>
                    </div>

                    <div style="height: 360px;">
                        <canvas id="mainLineChart"></canvas>
                    </div>

                    <div class="text-muted small mt-2">
                        Angka ditampilkan pada titik (agar mudah dibaca).
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-body">
                    <div class="fw-semibold mb-2">Info Data</div>

                    <ul class="mb-2 text-muted small">
                        <li>Semester: <b class="text-dark">{{ $selectedSemesterName }} ({{ $selectedSemesterYear }})</b></li>
                        <li>Sekolah: <b class="text-dark">{{ $selectedSchool }}</b></li>
                        <li>Kursus: <b class="text-dark">{{ $selectedCourse }}</b></li>
                        <li>Mentor : <b class="text-dark">{{ !empty($courseMeta['mentor_names']) ? implode(', ', $courseMeta['mentor_names']) : '-' }}</b></li>
                        <li>Jumlah Pertemuan: <b class="text-dark">{{ number_format((int)($cardsSafe[0]['value'] ?? 0)) }}</b></li>
                        <li>Siswa Aktif: <b class="text-dark">{{ number_format((int)($cardsSafe[2]['value'] ?? 0)) }}</b></li>
                    </ul>

                    <hr class="my-2">

                    {{-- Ringkasan Kehadiran (PERSENTASE, bukan count) --}}
                    <div class="fw-semibold mb-2">Ringkasan Kehadiran</div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Hadir</div>
                                <div class="fw-bold">{{ $pctHadir }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Tidak Hadir</div>
                                <div class="fw-bold">{{ $pctTdk }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Izin</div>
                                <div class="fw-bold">{{ $pctIzin }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Sakit</div>
                                <div class="fw-bold">{{ $pctSakit }}%</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- Ringkasan Nilai --}}
                    <div class="fw-semibold mb-2">Ringkasan Nilai</div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Rata-rata</div>
                                <div class="fw-bold">{{ $avgNilaiAll }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded">
                                <div class="text-muted small">Rentang</div>
                                <div class="fw-bold">{{ $minNilaiAll }}–{{ $maxNilaiAll }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-muted small mt-2">
                        Persentase dihitung dari total absensi (distinct user_id + meeting_id).
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== TABEL PESERTA (mengganti Pie + Fakta + Catatan cepat) ====== --}}
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 14px;">
                <div class="card-body">
                    <h5 class="mb-3">Daftar Peserta</h5>

                    @if(empty($participants) || (method_exists($participants,'count') && $participants->count() === 0))
                        <div class="alert alert-warning mb-0">
                            Belum ada data peserta untuk filter ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:60px;">No</th>
                                        <th style="width:240px;">Nama Lengkap</th>
                                        <th style="width:90px;">Kelas</th>
                                        <th style="width:220px;">Persentase Kehadiran</th>
                                        <th style="width:130px;">Total Nilai</th>
                                        <th style="width:130px;">Skala Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($participants as $idx => $p)
                                        @php
                                            $hadir = (int)($p->hadir ?? 0);
                                            $tidak = (int)($p->tidak_hadir ?? 0);
                                            $izin  = (int)($p->izin ?? 0);
                                            $sakit = (int)($p->sakit ?? 0);

                                            $den = $hadir + $tidak + $izin + $sakit;
                                            $persen = $den > 0 ? round(($hadir / $den) * 100, 1) : 0;

                                            $avg = isset($p->avg_score) ? (float)$p->avg_score : 0;

                                            if ($avg >= 95) $skala = 'Excellent';
                                            elseif ($avg >= 90) $skala = 'Very Good';
                                            elseif ($avg >= 80) $skala = 'Good';
                                            else $skala = 'Average';
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                @if($participants instanceof \Illuminate\Pagination\AbstractPaginator)
                                                    {{ ($participants->currentPage()-1)*$participants->perPage() + $idx + 1 }}
                                                @else
                                                    {{ $idx + 1 }}
                                                @endif
                                            </td>
                                            <td class="fw-semibold">{{ $p->name }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $p->kelas_name ?? '-' }}</div>
                                                @if(empty($courseId))
                                                    <small class="text-muted">{{ $p->course_name ?? '-' }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $persen }}%</div>
                                                <small class="text-muted">
                                                    Hadir: {{ $hadir }} • Tidak: {{ $tidak }} • Izin: {{ $izin }} • Sakit: {{ $sakit }}
                                                </small>
                                            </td>
                                            <td class="text-center fw-bold">{{ $avg }}</td>
                                            <td class="text-center">{{ $skala }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($participants instanceof \Illuminate\Pagination\AbstractPaginator)
                            <div class="mt-2">
                                {{ $participants->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Catatan (Resume Admin) --}}
    @php
        $isReadyResume = !empty($semesterId) && !empty($courseId);
        $isApproved = !empty($summaryApprovedAt);
    @endphp

    <div class="card shadow-sm mb-5" style="border-radius: 14px;">
    <div class="card-body">

        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-2">
        <div>
            <div class="fw-semibold">Kesimpulan (Resume Admin)</div>
            <div class="text-muted small">
            Resume tersimpan per kombinasi: <b>Semester</b> + <b>Course</b>.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($isApproved)
            <span class="badge bg-success">APPROVED</span>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($summaryApprovedAt)->format('d M Y H:i') }}
            </small>
            @else
            <span class="badge bg-warning text-dark">DRAFT</span>
            @endif
        </div>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        @if(!$isReadyResume)
        <div class="alert alert-warning mb-0">
            Resume hanya bisa dikelola jika <b>Semester</b> dan <b>Kursus</b> dipilih.
        </div>
        @else
        {{-- View-only: hasil resume --}}
        <div class="border rounded p-3 bg-light" style="min-height: 120px;">
            @if(!empty($summaryText))
            <div class="text-dark small">{!! nl2br(e($summaryText)) !!}</div>
            @else
            <div class="text-muted small">
                Belum ada resume untuk kombinasi filter ini. Klik <b>Edit Draft</b> untuk menambahkan.
            </div>
            @endif
        </div>

        {{-- Action buttons -> MODAL --}}
        <div class="d-flex justify-content-end gap-2 mt-3 flex-wrap">

            @if($isApproved)
                {{-- Kalau sudah approved: tidak boleh edit draft, harus unapprove dulu --}}
                <button type="button"
                        class="btn btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#modalResumeUnapprove">
                    Unapprove (Revisi)
                </button>
            @else
                {{-- Kalau masih draft: boleh edit & approve --}}
                <button type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalResumeDraft">
                    Edit Draft
                </button>

                <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalResumeApprove"
                        {{ empty($summaryText) ? 'disabled' : '' }}>
                    Approve
                </button>
            @endif

        </div>
        @endif

    </div>
    </div>


    {{-- =========================
    MODAL 1: DRAFT (EDIT/SAVE)
    ========================= --}}
    @if($isReadyResume)
    <div class="modal fade" id="modalResumeDraft" tabindex="-1" aria-labelledby="modalResumeDraftLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalResumeDraftLabel">Edit Draft Resume</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form method="POST" action="{{ route('admin.infografis.resume.save') }}">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semesterId }}">
            <input type="hidden" name="course_id" value="{{ $courseId }}">

            <div class="modal-body">
            @if($isApproved)
                <div class="alert alert-warning py-2">
                Resume saat ini sudah <b>Approved</b>. Jika kamu simpan perubahan, status akan kembali menjadi <b>Draft</b> (butuh approve ulang).
                </div>
            @endif

            <label class="form-label fw-semibold">Isi Resume</label>
            <textarea name="summary_text"
                        class="form-control"
                        rows="10"
                        placeholder="Tulis resume admin untuk Semester + Course ini...">{{ old('summary_text', $summaryText) }}</textarea>

            <div class="text-muted small mt-2">
                Tips: tulis ringkasan hasil belajar, kehadiran, tren nilai, dan catatan tindak lanjut.
            </div>
            </div>

            <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Draft</button>
            </div>
        </form>

        </div>
    </div>
    </div>
    @endif


    {{-- =========================
    MODAL 2: APPROVE
    ========================= --}}
    @if($isReadyResume)
    <div class="modal fade" id="modalResumeApprove" tabindex="-1" aria-labelledby="modalResumeApproveLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalResumeApproveLabel">Approve Resume</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form method="POST" action="{{ route('admin.infografis.resume.approve') }}">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semesterId }}">
            <input type="hidden" name="course_id" value="{{ $courseId }}">

            <div class="modal-body">
            <div class="text-muted small mb-2">
                Kamu akan menyetujui resume untuk kombinasi:
            </div>

            <ul class="small mb-3">
                <li>Semester ID: <b>{{ $semesterId }}</b></li>
                <li>Course ID: <b>{{ $courseId }}</b></li>
            </ul>

            <div class="border rounded p-2 bg-light small" style="max-height: 180px; overflow:auto;">
                {!! nl2br(e($summaryText ?? '-')) !!}
            </div>

            <div class="text-muted small mt-2">
                Setelah approve, badge akan berubah menjadi <b>APPROVED</b> dan ikut tampil di PDF export.
            </div>
            </div>

            <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success" {{ empty($summaryText) ? 'disabled' : '' }}>Approve</button>
            </div>
        </form>

        </div>
    </div>
    </div>
    @endif


    {{-- =========================
    MODAL 3: UNAPPROVE
    ========================= --}}
    @if($isReadyResume)
    <div class="modal fade" id="modalResumeUnapprove" tabindex="-1" aria-labelledby="modalResumeUnapproveLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalResumeUnapproveLabel">Unapprove Resume</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form method="POST" action="{{ route('admin.infografis.resume.unapprove') }}">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semesterId }}">
            <input type="hidden" name="course_id" value="{{ $courseId }}">

            <div class="modal-body">
            <div class="alert alert-danger py-2 mb-2">
                Ini akan membatalkan status <b>APPROVED</b> dan mengubah kembali menjadi <b>DRAFT</b>.
            </div>

            <div class="text-muted small">
                Isi resume tidak dihapus, hanya status approve yang dibatalkan.
            </div>
            </div>

            <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" {{ !$isApproved ? 'disabled' : '' }}>Unapprove</button>
            </div>
        </form>

        </div>
    </div>
    </div>
    @endif
</div>

{{-- ===================== CHARTS ===================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<script>
    const trend = @json($trendForJs);

    const labels = trend.map(p => p.x);
    const avgScore = trend.map(p => (p.avg_score === null || typeof p.avg_score === 'undefined') ? null : Number(p.avg_score));
    const hadir = trend.map(p => Number(p.hadir || 0));
    const tidakHadir = trend.map(p => Number(p.tidak_hadir || 0));
    const izin = trend.map(p => Number(p.izin || 0));
    const sakit = trend.map(p => Number(p.sakit || 0));

    function calcSummaryAt(i) {
        const h = hadir[i] || 0;
        const th = tidakHadir[i] || 0;
        const iz = izin[i] || 0;
        const sk = sakit[i] || 0;
        const total = h + th + iz + sk;
        const pct = total > 0 ? Math.round((h / total) * 100) : 0;
        return { h, th, iz, sk, total, pct };
    }

    // ===== Line Chart (Main) =====
    const elLine = document.getElementById('mainLineChart');
    if (elLine && typeof Chart !== 'undefined') {
        if (typeof ChartDataLabels !== 'undefined') {
            Chart.register(ChartDataLabels);
        }

        new Chart(elLine, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    // URUTAN LEGEND: Nilai, Hadir, Tidak Hadir, Izin, Sakit
                    { label: 'Nilai (Avg)', data: avgScore, borderColor: 'green', backgroundColor: 'green', tension: 0.35, borderWidth: 2, pointRadius: 3, spanGaps: true },
                    { label: 'Hadir', data: hadir, borderColor: 'blue', backgroundColor: 'blue', tension: 0.35, borderWidth: 2, pointRadius: 3 },
                    { label: 'Tidak Hadir', data: tidakHadir, borderColor: 'red', backgroundColor: 'red', tension: 0.35, borderWidth: 2, pointRadius: 3 },
                    { label: 'Izin', data: izin, borderColor: 'purple', backgroundColor: 'purple', tension: 0.35, borderWidth: 2, pointRadius: 3 },
                    { label: 'Sakit', data: sakit, borderColor: 'orange', backgroundColor: 'orange', tension: 0.35, borderWidth: 2, pointRadius: 3 },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            title: (items) => `X: ${items?.[0]?.label ?? '-'}`,
                            label: (ctx) => {
                                const ds = ctx.dataset.label;
                                const v = ctx.raw;
                                if (ds.includes('Nilai')) return `${ds}: ${v === null ? '-' : v}`;
                                return `${ds}: ${v}`;
                            },
                            afterBody: (items) => {
                                if (!items || !items.length) return [];
                                const i = items[0].dataIndex;
                                const s = calcSummaryAt(i);
                                const nilai = avgScore[i];
                                return [
                                    '',
                                    'Ringkasan titik:',
                                    `• Total: ${s.total}`,
                                    `• % Hadir: ${s.pct}%`,
                                    `• Hadir: ${s.h}`,
                                    `• Tidak Hadir: ${s.th}`,
                                    `• Izin: ${s.iz}`,
                                    `• Sakit: ${s.sk}`,
                                    `• Nilai Avg: ${nilai === null ? '-' : nilai}`,
                                ];
                            }
                        }
                    },
                    datalabels: (typeof ChartDataLabels !== 'undefined') ? {
                        // tampilkan angka di titik (agar terbaca)
                        // biar tidak terlalu penuh: tampilkan angka untuk Nilai saja
                        display: (context) => context.datasetIndex === 0,
                        align: 'top',
                        anchor: 'end',
                        offset: 4,
                        formatter: (value) => {
                            if (value === null || value === undefined) return '';
                            return value;
                        },
                        font: { size: 10 }
                    } : {}
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
</script>
@endsection
