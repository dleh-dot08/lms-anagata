@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Detail Absensi</h2>
    <!-- Add this to your <head> section or in the content of the page -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    {{-- Detail Absensi --}}
    <div class="mb-4">
        <a href="{{ route('attendances.admin.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Absensi: {{ $attendance->user->name }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Nama:</strong> {{ $attendance->user->name }}</p>
            <p><strong>Kursus:</strong> {{ $attendance->course->nama_kelas ?? '-' }}</p>
            <p><strong>Tanggal Absensi:</strong> {{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="badge 
                    @if($attendance->status == 'Hadir') bg-success
                    @elseif($attendance->status == 'Izin') bg-warning
                    @elseif($attendance->status == 'Sakit') bg-primary
                    @else bg-danger @endif">
                    {{ $attendance->status }}
                </span>
            </p>

            {{-- Menampilkan Foto atau TTD Digital --}}
            <div class="row">
                @if($attendance->file_attache)
                <div class="col-md-6 mb-3">
                    <strong>Foto Kehadiran:</strong><br>
                    <img src="{{ asset('storage/' . $attendance->file_attache) }}" alt="Foto Kehadiran" class="img-fluid border p-2">
                </div>
                @endif
            
                @if($attendance->ttd_digital)
                <div class="col-md-6 mb-3">
                    <strong>Tanda Tangan Digital:</strong><br>
                    <img src="{{ asset('storage/' . $attendance->ttd_digital) }}" alt="Tanda Tangan" class="img-fluid border p-2">
                </div>
                @endif
            </div>

            {{-- Peta Lokasi --}}
            @if($attendance->longitude && $attendance->latitude)
            <div id="map" style="height: 400px;"></div>
            <script>
                var map = L.map('map').setView([{{ $attendance->latitude }}, {{ $attendance->longitude }}], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([{{ $attendance->latitude }}, {{ $attendance->longitude }}]).addTo(map)
                    .bindPopup("<b>{{ $attendance->user->name }}</b><br>{{ $attendance->status }}<br>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y') }}")
                    .openPopup();
            </script>
            @else
            <p><strong>Lokasi tidak tersedia.</strong></p>
            @endif
        </div>
    </div>

    <a href="{{ route('attendances.admin.index') }}" class="btn btn-secondary mt-4">Kembali</a>
</div>
@endsection
