@extends('layouts.karyawan.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card" style="background: url('{{ asset('assets/img/illustrations/Header.png') }}') center/cover no-repeat;">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h3 class="card-title text-primary"><b>Halo, Selamat Datang! &#128516; &#10024;</b></h3>
                            <p class="mb-4">
                                Anda telah menjadi, <span class="fw-bold">Peserta.</span>
                                <br>sekarang anda bisa
                                <br>mengakses modul yang ada
                                <br>dalam website
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                alt="View Badge User"
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
                    <h2 id="clock" class="fw-bold" style="font-size: 48px; letter-spacing: 2px; color: #ffffff;">
                    </h2>
                    <p id="date" style="font-size: 16px; color: #ffffff;"></p>
                </div>
            </div>
        </div>
    </div>

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

        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Run once immediately to display the time right away
    </script>
@endsection
