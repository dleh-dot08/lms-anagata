@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ROW: Welcome + Clock --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card" style="background: url('{{ asset('assets/img/illustrations/Header.png') }}') center/cover no-repeat;">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h3 class="card-title text-primary">
                                <b>Halo, Selamat Datang! &#128516; &#10024;</b>
                            </h3>
                            <p class="mb-4">
                                Anda masuk sebagai, <span class="fw-bold">Sekolah.</span>
                                <br>Anda bisa memantau peserta,
                                <br>nilai, absensi, dan catatan mentor
                                <br>untuk sekolah Anda.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                                 height="140"
                                 alt="Sekolah"
                                 data-app-dark-img="{{ asset('assets/img/illustrations/man-with-laptop-dark.png') }}"
                                 data-app-light-img="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clock --}}
        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow-lg p-2"
                 style="border-radius: 10px; background: linear-gradient(135deg, #667eea, #24a0e7); color: #ffffff;">
                <div class="card-body">
                    <h5 class="card-title" style="color: #ffffff;">Waktu Saat Ini</h5>
                    <h2 id="clock" class="fw-bold" style="font-size: 48px; letter-spacing: 2px; color: #ffffff;"></h2>
                    <p id="date" style="font-size: 16px; color: #ffffff;"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row mt-1">
        <div class="col-12 mb-2">
            <h5 class="mb-0">Ringkasan Sekolah</h5>
            <small class="text-muted">Statistik utama sekolah Anda.</small>
        </div>

        @php $cardsSafe = $cards ?? []; @endphp

        @if(count($cardsSafe) === 0)
            <div class="col-12">
                <div class="alert alert-warning">
                    Data ringkasan belum tersedia. Pastikan controller mengirim variabel <b>$cards</b>.
                </div>
            </div>
        @else
            @foreach($cardsSafe as $c)
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card shadow-sm h-100" style="border-radius: 14px;">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <div class="text-muted" style="font-size: 13px;">
                                        {{ $c['label'] ?? '-' }}
                                    </div>
                                    <div class="fw-bold" style="font-size: 28px; line-height: 1.2;">
                                        @php
                                            $val = $c['value'] ?? 0;
                                        @endphp

                                        {{-- support angka dan persen --}}
                                        @if(is_string($val) && str_contains($val, '%'))
                                            {{ $val }}
                                        @else
                                            {{ is_numeric($val) ? number_format($val) : $val }}
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-label-primary">KPI</span>
                            </div>

                            @if(!empty($c['hint']))
                                <div class="mt-2"><small class="text-muted">{{ $c['hint'] }}</small></div>
                            @else
                                <div class="mt-2"><small class="text-muted">Terupdate saat halaman dimuat</small></div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Charts Row --}}
    <div class="row">

        {{-- NILAI --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 14px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Infografis Nilai</h6>
                        <small class="text-muted">Rata-rata <b>total_score</b> (30 hari terakhir).</small>
                    </div>
                    <span class="badge bg-label-success">Nilai</span>
                </div>
                <div class="card-body">
                    <canvas id="nilaiChart" height="140"></canvas>

                    @if(empty($nilaiSeries) || count($nilaiSeries) === 0)
                        <div class="alert alert-secondary mt-3 mb-0">
                            Belum ada data nilai untuk ditampilkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ABSENSI --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm" style="border-radius: 14px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Infografis Absensi</h6>
                        <small class="text-muted">Hadir vs Tidak Hadir (14 hari terakhir).</small>
                    </div>
                    <span class="badge bg-label-info">Absensi</span>
                </div>
                <div class="card-body">
                    <canvas id="absensiChart" height="140"></canvas>

                    @if(empty($absensiTrend) || count($absensiTrend) === 0)
                        <div class="alert alert-secondary mt-3 mb-0">
                            Belum ada data absensi untuk ditampilkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Notes + Top Students --}}
    <div class="row">

        {{-- CATATAN MENTOR --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm" style="border-radius: 14px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Catatan Mentor Terbaru</h6>
                        <small class="text-muted">Ringkasan catatan per pertemuan.</small>
                    </div>
                    <span class="badge bg-label-warning">Catatan</span>
                </div>
                <div class="card-body">

                    <div class="card-body">
                        @php $notes = $mentorNotes ?? []; @endphp

                        @if(count($notes) === 0)
                            <div class="alert alert-secondary mb-0">
                                Belum ada catatan mentor yang ditampilkan.
                            </div>
                        @else

                            {{-- container scroll --}}
                            <div style="max-height: 380px; overflow-y: auto; padding-right: 6px;">
                                <div class="list-group">
                                    @foreach($notes as $n)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between">
                                                <div class="fw-semibold">
                                                    {{ $n['student_name'] ?? 'Catatan' }}
                                                    @if(!empty($n['kelas']))
                                                        <span class="text-muted fw-normal">• {{ $n['kelas'] }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $n['date'] ?? '' }}</small>
                                            </div>

                                            @if(!empty($n['mentor_name']))
                                                <small class="text-muted">Mentor: {{ $n['mentor_name'] }}</small>
                                            @endif

                                            {{-- Batasi panjang teks per item --}}
                                            <div class="mt-2 text-muted" style="
                                                display: -webkit-box;
                                                -webkit-line-clamp: 3;
                                                -webkit-box-orient: vertical;
                                                overflow: hidden;
                                            ">
                                                {{ $n['note'] ?? '-' }}
                                            </div>

                                            @if(!empty($n['tag']))
                                                <div class="mt-2">
                                                    <span class="badge bg-label-primary">{{ $n['tag'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TOP PESERTA --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Peserta Paling Aktif</h6>
                        <small class="text-muted">Berdasarkan jumlah absensi (30 hari).</small>
                    </div>
                    <span class="badge bg-label-primary">Top</span>
                </div>
                <div class="card-body">
                    @php $top = $topStudents ?? []; @endphp

                    @if(count($top) === 0)
                        <div class="alert alert-secondary mb-0">
                            Data top peserta belum tersedia.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Peserta</th>
                                        <th class="text-end">Aktivitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top as $t)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $t['name'] ?? '-' }}</div>
                                                @if(!empty($t['kelas']))
                                                    <small class="text-muted">{{ $t['kelas'] }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">{{ $t['count'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-2">
                        <a href="{{ url('/sekolah/peserta') }}" class="btn btn-sm btn-primary">
                            Lihat Peserta
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Clock Script --}}
<script>
    function updateClock() {
        var now = new Date();
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');
        var seconds = now.getSeconds().toString().padStart(2, '0');
        var timeString = hours + ':' + minutes + ':' + seconds;

        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
            'November', 'December'
        ];
        var day = days[now.getDay()];
        var month = months[now.getMonth()];
        var date = now.getDate().toString().padStart(2, '0');
        var fullDate = day + ', ' + date + ' ' + month + ' ' + now.getFullYear();

        document.getElementById('clock').textContent = timeString;
        document.getElementById('date').textContent = fullDate;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // ===== NILAI =====
    const nilaiSeries = @json($nilaiSeries ?? []);
    const nilaiLabels = nilaiSeries.map(x => x.label);
    const nilaiValues = nilaiSeries.map(x => Number(x.value || 0));

    const nilaiCtx = document.getElementById('nilaiChart');
    if (nilaiCtx) {
        new Chart(nilaiCtx, {
            type: 'bar',
            data: {
                labels: nilaiLabels,
                datasets: [{
                    label: 'Rata-rata total_score',
                    data: nilaiValues,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // ===== ABSENSI =====
    const absensiTrend = @json($absensiTrend ?? []);
    const absLabels = absensiTrend.map(x => x.d);
    const hadirVals = absensiTrend.map(x => Number(x.hadir || 0));
    const alfaVals  = absensiTrend.map(x => Number(x.alfa || 0));

    const absCtx = document.getElementById('absensiChart');
    if (absCtx) {
        new Chart(absCtx, {
            type: 'line',
            data: {
                labels: absLabels,
                datasets: [
                    { label: 'Hadir', data: hadirVals, tension: 0.35, borderWidth: 2, pointRadius: 3 },
                    { label: 'Tidak Hadir', data: alfaVals, tension: 0.35, borderWidth: 2, pointRadius: 3 },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }
</script>
@endsection
