@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <h4>Absen untuk Kelas: {{ $activity->nama_kegiatan }}</h4>
    <form id="absenForm" action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="activity_id" value="{{ $activity->id }}">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="photo_capture" id="photo_file">

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
            <label class="form-label">Ambil Foto Kehadiran</label><br>
            <video id="camera" width="320" height="240" autoplay style="border:1px solid #ccc;"></video>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <br>
            <button type="button" class="btn btn-primary mt-2" onclick="takePhoto()">Ambil Foto</button>
            <div id="preview" class="mt-2"></div>
        </div>

        <div class="mb-3">
            <label for="ttd_canvas" class="form-label">Tanda Tangan Digital (Opsional)</label><br>
            <canvas id="ttd_canvas" width="300" height="100" style="border:1px solid #ccc;"></canvas>
            <input type="hidden" name="ttd_digital" id="ttd_digital_file">
            <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="clearSignature()">Bersihkan</button>
        </div>

        <div id="map" style="height: 300px; margin-bottom: 20px;"></div>

        <button type="submit" class="btn btn-success">Submit Absensi</button>
    </form>
</div>

<script>
    // Lokasi Map
    var map = L.map('map').setView([0, 0], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function onLocationFound(e) {
        var radius = e.accuracy / 2;
        L.marker(e.latlng).addTo(map).bindPopup("Anda berada di sini").openPopup();
        L.circle(e.latlng, radius).addTo(map);

        document.getElementById('latitude').value = e.latitude;
        document.getElementById('longitude').value = e.longitude;
    }

    function onLocationError(e) {
        alert('Lokasi harus diaktifkan untuk absen.');
    }

    map.locate({ setView: true, maxZoom: 16, watch: true, enableHighAccuracy: true });
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    // Kamera
    const camera = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const preview = document.getElementById('preview');
    const photoInput = document.getElementById('photo_file');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false })
        .then(stream => camera.srcObject = stream)
        .catch(err => alert('Gagal mengakses kamera: ' + err));

    function takePhoto() {
        const context = canvas.getContext('2d');
        context.drawImage(camera, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/jpeg');
        photoInput.value = imageData;

        preview.innerHTML = `<img src="${imageData}" width="160">`;
    }

    // Tanda tangan digital
    const ttdCanvas = document.getElementById('ttd_canvas');
    const ctx = ttdCanvas.getContext('2d');
    let isDrawing = false;

    ttdCanvas.addEventListener('mousedown', () => isDrawing = true);
    ttdCanvas.addEventListener('mouseup', () => {
        isDrawing = false;
        document.getElementById('ttd_digital_file').value = ttdCanvas.toDataURL("image/png");
    });
    ttdCanvas.addEventListener('mousemove', drawSignature);

    function drawSignature(e) {
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
        ctx.clearRect(0, 0, ttdCanvas.width, ttdCanvas.height);
        document.getElementById('ttd_digital_file').value = '';
    }

    // Validasi sebelum submit
    document.getElementById('absenForm').addEventListener('submit', function(e) {
    if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Lokasi Belum Aktif!',
            text: 'Aktifkan lokasi Anda sebelum mengirim absensi.',
            showConfirmButton: true,
            confirmButtonText: 'Coba Lagi',
            timer: 4000,
            timerProgressBar: true
        });
        return;
    }

    if (!document.getElementById('photo_file').value) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Foto Tidak Terdeteksi!',
            text: 'Silakan ambil foto kehadiran sebelum submit absensi.',
            showConfirmButton: true,
            confirmButtonText: 'Ambil Foto',
            timer: 4000,
            timerProgressBar: true
        });
        return;
    }
});
</script>
@endsection
