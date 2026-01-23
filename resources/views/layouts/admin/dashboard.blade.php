@extends('layouts.admin.template')

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
                                Anda telah menjadi, <span class="fw-bold">Administrator.</span>
                                <br>sekarang anda bisa
                                <br>mengatur semua isi
                                <br>dalam website
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}"
                                 height="140"
                                 alt="Admin"
                                 data-app-dark-img="{{ asset('assets/img/illustrations/man-with-laptop-dark.png') }}"
                                 data-app-light-img="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

    {{-- ROW: KPI Cards --}}
    <div class="row mt-1">
        <div class="col-12 mb-2">
            <h5 class="mb-0">Ringkasan</h5>
            <small class="text-muted">Statistik cepat untuk memantau aktivitas & pertumbuhan.</small>
        </div>

        @php
            $cardsSafe = $cards ?? [];
        @endphp

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
                                        {{ isset($c['value']) ? number_format($c['value']) : '0' }}
                                    </div>
                                </div>
                                <span class="badge bg-label-primary">KPI</span>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Terupdate saat halaman dimuat</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ROW: Charts --}}
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm" style="border-radius: 14px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0">Tren Peserta Baru (14 Hari)</h6>
                        <small class="text-muted">Jumlah pendaftaran peserta per hari (role peserta).</small>
                    </div>
                    <span class="badge bg-label-info">Chart</span>
                </div>
                <div class="card-body">
                    <canvas id="trendPesertaChart" height="120"></canvas>

                    <div class="mt-2">
                        <small class="text-muted">
                            Jika grafik kosong, berarti belum ada data atau variabel <b>$trendPeserta</b> belum dikirim.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side panel --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header">
                    <h6 class="mb-0">Catatan Admin</h6>
                    <small class="text-muted">Checklist ringkas operasional.</small>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Pastikan data sekolah & kelas sudah lengkap.</li>
                        <li>Cek peserta aktif (30 hari) untuk monitoring adopsi.</li>
                        <li>Review e-raport yang sudah terbit.</li>
                        <li>Pantau tren pendaftaran peserta.</li>
                    </ul>
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

{{-- Chart.js (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    // Prepare trend data
    const trendRaw = @json($trendPeserta ?? []);
    const labels = trendRaw.map(x => x.d);
    const values = trendRaw.map(x => Number(x.c || 0));

    const ctx = document.getElementById('trendPesertaChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peserta Baru',
                    data: values,
                    tension: 0.35,
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 3,
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
</script>
@endsection
