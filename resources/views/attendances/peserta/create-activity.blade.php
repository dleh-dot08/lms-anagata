@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h4>Absen untuk Kegiatan: {{ $activity->nama_kegiatan }}</h4>
    <form action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="activity_id" value="{{ $activity->id }}">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="mb-3">
            <label for="status" class="form-label">Status Kehadiran</label>
            <select name="status" id="status" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Hadir">Hadir</option>
                <option value="Tidak Hadir">Tidak Hadir</option>
                <option value="Izin">Izin</option>
                <option value="Sakit">Sakit</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="file_attache" class="form-label">Foto Kehadiran (Opsional)</label>
            <input type="file" name="file_attache" class="form-control">
        </div>

        <div class="mb-3">
            <label for="ttd_canvas" class="form-label">Tanda Tangan Digital</label><br>
            <canvas id="ttd_canvas" width="300" height="100" style="border:1px solid #ccc;"></canvas>
            <input type="hidden" name="ttd_digital" id="ttd_digital_file">
            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="clearSignature()">Bersihkan</button>
        </div>

        <div id="map" style="height: 300px; margin-bottom: 20px;"></div>

        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>

<script>
    // Inisialisasi peta
    var map = L.map('map').setView([0, 0], 13); // Koordinat default

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function onLocationFound(e) {
        var radius = e.accuracy / 2;

        L.marker(e.latlng).addTo(map)
            .bindPopup("Anda berada di sini").openPopup();

        L.circle(e.latlng, radius).addTo(map);

        document.getElementById('latitude').value = e.latitude;
        document.getElementById('longitude').value = e.longitude;
    }

    function onLocationError(e) {
        alert(e.message);
    }

    map.locate({ setView: true, maxZoom: 16, watch: true, enableHighAccuracy: true });
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    const canvas = document.getElementById('ttd_canvas');
    const ctx = canvas.getContext('2d');
    let isDrawing = false;

    canvas.addEventListener('mousedown', () => isDrawing = true);
    canvas.addEventListener('mouseup', () => {
        isDrawing = false;
        document.getElementById('ttd_digital_file').value = canvas.toDataURL("image/png");
    });
    canvas.addEventListener('mousemove', draw);

    function draw(e) {
        if (!isDrawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    }

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('ttd_digital_file').value = '';
    }
</script>
@endsection
