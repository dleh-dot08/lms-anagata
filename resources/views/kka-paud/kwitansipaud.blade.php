<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cek Kwitansi PAUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body { background-color: #f0f2f5; }
        .navbar-brand img { height: 40px; }
        .card { border-radius: 12px; }
        .card-header { border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('koding_ka25/logo_all.png') }}" alt="Anagata Academy Logo">
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ url('/kka-paud') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="/kka-paud/cek-pendaftaran">Cek Status Pendaftaran</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id/faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id">LMS-RuangAnagata</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://forms.gle/c32pQRw6dSW2TTqF8">Daftar Peserta</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" data-bs-toggle="dropdown">Pembayaran</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('kka-paud.invoice') }}">Unduh Invoice</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="https://forms.gle/y99iyuTFV1vWDMv78">Upload Bukti Pembayaran</a></li>
                            <li><a class="dropdown-item" href="{{ route('kka-paud.kwitansi') }}">Unduh Receipt</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h3 class="fw-bold mb-0">Cek Kwitansi PAUD</h3>
                    </div>
                    <div class="card-body p-4">
                        <ol>
                            <li>Masukkan <strong>NPSN</strong> sesuai data pendaftaran.</li>
                            <li>Klik tombol <strong>"Cari Kwitansi"</strong>.</li>
                            <li>Jika ditemukan, detail kwitansi akan muncul dan bisa diunduh.</li>
                        </ol>

                        <form id="kwitansiForm">
                            <div class="mb-3">
                                <label for="npsn" class="form-label fw-semibold">Masukkan NPSN Sekolah</label>
                                <input type="text" class="form-control" id="npsn" placeholder="Contoh: 12345678" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Cek Kwitansi</button>
                        </form>

                        <div id="alertBox" class="mt-4" style="display:none;"></div>
                        <div id="resultTable" class="mt-3" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('kwitansiForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const npsn = document.getElementById('npsn').value.trim();
            const alertBox = document.getElementById('alertBox');
            const resultTable = document.getElementById('resultTable');

            fetch("{{ route('kka-paud.cekKwitansi') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ npsn })
            })
            .then(res => res.json())
            .then(data => {
                alertBox.style.display = 'block';
                resultTable.style.display = 'none';
                resultTable.innerHTML = '';

                if (data.success && data.status === 'sudah') {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = `✅ ${data.message}`;

                    if (data.data) {
                        resultTable.style.display = 'block';
                        resultTable.innerHTML = `
                            <table class="table table-bordered">
                                <tr><th>Nama Peserta</th><td>${data.data.nama_peserta}</td></tr>
                                <tr><th>NPSN</th><td>${data.data.npsn}</td></tr>
                                <tr><th>Nama PAUD</th><td>${data.data.nama_paud}</td></tr>
                                <tr><th>No Invoice</th><td>${data.data.nomor_invoice}</td></tr>
                                <tr><th>No Receipt</th><td>${data.data.nomor_receipt}</td></tr>
                                <tr>
                                    <th>Unduh Kwitansi</th>
                                    <td>
                                        <a href="${data.data.url_kwitansi}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success">
                                            <i class="bi bi-receipt"></i> Unduh
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        `;
                    }
                } else if (data.success && data.status === 'belum') {
                    alertBox.className = 'alert alert-warning';
                    alertBox.innerHTML = `⚠️ ${data.message}`;
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = `❌ ${data.message}`;
                }
            })
            .catch(err => {
                alertBox.style.display = 'block';
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = `❌ Terjadi kesalahan: ${err.message}`;
            });
        });
    </script>
</body>
</html>
