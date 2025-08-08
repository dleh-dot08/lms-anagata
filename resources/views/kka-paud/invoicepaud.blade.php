<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cek Invoice PAUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f0f2f5;
        }
        .navbar-brand img {
            height: 40px;
        }
        .card {
            border-radius: 12px;
        }
        .card-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('koding_ka25/logo_all.png') }}" alt="Anagata Academy Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav gap-3">
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ url('/kka-paud') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id/faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id">LMS-RuangAnagata</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://forms.gle/c32pQRw6dSW2TTqF8">Daftar Peserta</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" role="button" data-bs-toggle="dropdown">Pembayaran</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('kka-paud.invoice') }}">Unduh Invoice</a></li>
                            <li><a class="dropdown-item" href="#">Unduh Receipt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Upload Bukti Pembayaran</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h3 class="fw-bold mb-0">Cek Invoice PAUD</h3>
                    </div>
                    <div class="card-body p-4">
                        <form id="invoiceForm">
                            <div class="mb-3">
                                <label for="npsn" class="form-label fw-semibold">Masukkan NPSN Sekolah</label>
                                <input type="text" class="form-control" id="npsn" name="npsn" placeholder="Contoh: 12345678" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Cek Invoice</button>
                        </form>

                        <div id="result" class="mt-4" style="display:none;">
                            <div class="alert" role="alert"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AJAX Script --}}
    <script>
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const npsn = document.getElementById('npsn').value;
            const resultDiv = document.getElementById('result');
            const alertBox = resultDiv.querySelector('.alert');

            fetch("{{ route('kka-paud.cekInvoice') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ npsn })
            })
            .then(res => res.json())
            .then(data => {
                resultDiv.style.display = 'block';
                if(data.success && data.status === 'sudah') {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = '✅ ' + data.message;
                } else if(data.success && data.status === 'belum') {
                    alertBox.className = 'alert alert-warning';
                    alertBox.innerHTML = '⚠️ ' + data.message;
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = '❌ ' + data.message;
                }
            })
            .catch(err => {
                resultDiv.style.display = 'block';
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = '❌ Terjadi kesalahan: ' + err.message;
            });
        });
    </script>
</body>
</html>
