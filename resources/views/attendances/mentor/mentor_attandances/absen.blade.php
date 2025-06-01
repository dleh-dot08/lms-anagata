@extends('layouts.mentor.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-calendar-check me-2"></i>Absen Mengajar</h5>
            <small class="text-muted float-end">Form Kehadiran Mentor</small>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <p><i class="bx bx-book-open text-primary me-1"></i> <strong>Kursus:</strong> {{ $course->nama_kelas }}</p>
                <p><i class="bx bx-calendar text-primary me-1"></i> <strong>Pertemuan:</strong> {{ $meeting->judul }} (Ke-{{ $meeting->pertemuan }})</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}</div>
            @elseif (session('warning'))
                <div class="alert alert-warning"><i class="bx bx-info-circle me-1"></i> {{ session('warning') }}</div>
            @elseif (session('error'))
                <div class="alert alert-danger"><i class="bx bx-error-circle me-1"></i> {{ session('error') }}</div>
            @endif

            <form id="absenForm" action="{{ route('mentor.absen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">
                <input type="hidden" name="meeting_id" value="{{ $meeting->id }}">
                <input type="hidden" name="tanggal" value="{{ \Carbon\Carbon::today()->toDateString() }}">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="photo_capture" id="photo_file">

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label" for="status"><i class="bx bx-user-check me-1"></i> Status Kehadiran</label>
                    <div class="col-sm-10">
                        <select name="status" id="status" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Tidak Hadir">Tidak Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                        @error('status')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3" id="waktu-absen-group" style="display: none;">
                    <label class="col-sm-2 col-form-label" for="waktu_absen"><i class="bx bx-time me-1"></i> Waktu Mengajar</label>
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="waktu_absen" id="waktu_absen">
                        <p id="displayed_waktu_absen" class="form-control-static"></p>
                        @error('waktu_absen')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3" id="foto-kehadiran-group">
                    <label class="col-sm-2 col-form-label"><i class="bx bx-camera me-1"></i> Foto Kehadiran</label>
                    <div class="col-sm-10">
                        <video id="camera" width="320" height="240" autoplay style="border:1px solid #ccc;"></video>
                        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                        <br>
                        <button type="button" class="btn btn-primary mt-2" onclick="takePhoto()">Ambil Foto</button>
                        <div id="preview" class="mt-2"></div>
                        @error('photo_capture')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmModal"><i class="bx bx-save me-1"></i> Simpan Absen</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ms-2"><i class="bx bx-arrow-back me-1"></i> Kembali</a>
                    </div>
                </div>

                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Kehadiran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Yakin dengan kehadiran Anda?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let stream = null; // Declare stream globally to manage camera state

    document.addEventListener("DOMContentLoaded", function () {
        const statusSelect = document.getElementById('status');
        const fotoKehadiranGroup = document.getElementById('foto-kehadiran-group');
        // Removed lokasiKehadiranGroup as it's no longer displayed/needed for toggling
        const cameraElement = document.getElementById('camera');
        const previewElement = document.getElementById('preview');
        const photoFileInput = document.getElementById('photo_file');
        const waktuAbsenInput = document.getElementById('waktu_absen');
        const displayedWaktuAbsen = document.getElementById('displayed_waktu_absen');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        // Initial setup on page load
        updateWaktuAbsen(); // Set attendance time immediately
        getLocation(); // Get location on page load
        toggleElements(); // Set initial state based on default 'status'

        statusSelect.addEventListener('change', toggleElements);

        function toggleElements() {
            if (statusSelect.value === 'Hadir') {
                fotoKehadiranGroup.style.display = 'flex';
                initCamera(); // Start camera
            } else {
                fotoKehadiranGroup.style.display = 'none';
                stopCamera(); // Stop camera
                // Clear any previous photo and preview if status changes from Hadir
                photoFileInput.value = '';
                previewElement.innerHTML = '';
            }
        }

        // Function to update the hidden time input and display it
        function updateWaktuAbsen() {
            const now = new Date();
            const year = now.getFullYear();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');

            const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            waktuAbsenInput.value = formattedDateTime; // Set nilai input hidden

            const displayOptions = {
                year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
                hour12: false // For 24-hour format
            };
            displayedWaktuAbsen.textContent = now.toLocaleString('id-ID', displayOptions);
        }

        // Initialize Camera
        function initCamera() {
            if (stream) {
                cameraElement.srcObject = stream;
                return;
            }

            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" }, audio: false })
                .then(videoStream => {
                    stream = videoStream;
                    cameraElement.srcObject = stream;
                })
                .catch(err => {
                    console.error('Gagal mengakses kamera:', err);
                    alert('Gagal mengakses kamera: ' + err.message + '. Pastikan browser Anda mengizinkan akses kamera dan Anda menggunakan HTTPS.');
                    fotoKehadiranGroup.style.display = 'none';
                });
        }

        // Stop Camera
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                cameraElement.srcObject = null;
            }
        }

        // Get Location using navigator.geolocation
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latitudeInput.value = position.coords.latitude;
                        longitudeInput.value = position.coords.longitude;
                        console.log('Lokasi berhasil didapatkan:', position.coords.latitude, position.coords.longitude);
                    },
                    function(error) {
                        console.error('Error mendapatkan lokasi:', error.message);
                        let errorMessage = 'Gagal mendapatkan lokasi otomatis: ' + error.message;
                        if (error.code === error.PERMISSION_DENIED) {
                            errorMessage += '. Pastikan Anda mengizinkan akses lokasi browser.';
                        } else if (error.code === error.POSITION_UNAVAILABLE) {
                            errorMessage += '. Lokasi Anda tidak tersedia atau tidak dapat diakses saat ini.';
                        } else if (error.code === error.TIMEOUT) {
                            errorMessage += '. Waktu tunggu untuk mendapatkan lokasi habis.';
                        }
                        alert(errorMessage);
                        // Clear location if there's an error, preventing submission without location
                        latitudeInput.value = '';
                        longitudeInput.value = '';
                    },
                    {
                        enableHighAccuracy: true, // Try to get the most accurate location
                        timeout: 10000, // Wait up to 10 seconds for a location
                        maximumAge: 0 // Don't use a cached position
                    }
                );
            } else {
                alert('Geolocation tidak didukung oleh browser Anda.');
                latitudeInput.value = '';
                longitudeInput.value = '';
            }
        }
        // Removed initMap(), addMarker(), destroyMap() functions
    });

    // Take Photo
    function takePhoto() {
        const camera = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');
        const photoInput = document.getElementById('photo_file');

        if (!stream || !camera.srcObject) {
            alert('Kamera belum aktif atau gagal diinisialisasi. Pastikan status "Hadir" dipilih dan izinkan akses kamera.');
            return;
        }

        const context = canvas.getContext('2d');
        canvas.width = camera.videoWidth;
        canvas.height = camera.videoHeight;
        context.drawImage(camera, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        photoInput.value = imageData;

        preview.innerHTML = `<img src="${imageData}" width="160" class="img-thumbnail">`;
    }

    // Validation before submit
    document.getElementById('absenForm').addEventListener('submit', function(e) {
        if (document.getElementById('status').value === 'Hadir') {
            if (!document.getElementById('photo_file').value) {
                e.preventDefault();
                alert('Silakan ambil foto kehadiran terlebih dahulu.');
                return false;
            }
            // Validate if location inputs are filled when status is 'Hadir'
            if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
                e.preventDefault();
                alert('Lokasi Anda tidak terdeteksi. Pastikan Anda mengizinkan akses lokasi.');
                return false;
            }
        }
        return true;
    });
</script>
@endsection