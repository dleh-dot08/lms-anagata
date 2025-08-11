<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cek Status Pendaftaran</title>
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
        .status-badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.9rem;
        }
        .status-waiting {
            background-color: #ffc107;
            color: #212529;
        }
        .status-approved {
            background-color: #28a745;
            color: #fff;
        }
        .table th,
        .table td {
            vertical-align: middle !important;
        }
        .table thead {
            background-color: #e9ecef;
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
                    </li><li class="nav-item"><a class="nav-link fw-semibold text-dark" href="/kka-paud/cek-pendaftaran">Cek Status Pendaftaran</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id/faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://ruanganagata.id">LMS-RuangAnagata</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="https://forms.gle/c32pQRw6dSW2TTqF8">Daftar Peserta</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" role="button" data-bs-toggle="dropdown">Pembayaran</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('kka-paud.invoice') }}">Unduh Invoice</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="https://forms.gle/aVyzgGRFqsKwbNuGA">Upload Bukti Pembayaran</a></li>
                            <li><a class="dropdown-item" href="{{ route('kka-paud.kwitansi') }}">Unduh Receipt</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="fw-bold mb-0">Cek Status Pendaftaran Guru</h3>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($error_message))
                            <div class="alert alert-danger text-center" role="alert">
                                {{ $error_message }}
                            </div>
                        @endif

                        {{-- PENCARIAN NAMA --}}
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari Nama Peserta...">
                        </div>

                        {{-- TABEL DATA --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        @if(isset($header) && !empty($header))
                                            @foreach($header as $column)
                                                <th class="text-center">{{ $column }}</th>
                                            @endforeach
                                        @else
                                            <th class="text-center">Tidak ada header</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($data) && count($data) > 0)
                                        @foreach($data as $row)
                                            <tr>
                                                @foreach($row as $key => $cell)
                                                    <td class="text-center">
                                                        @if(is_string($cell) && strtolower(trim($cell)) == 'menunggu')
                                                            <span class="status-badge status-waiting">Menunggu</span>
                                                        @elseif(is_string($cell) && strtolower(trim($cell)) == 'disetujui')
                                                            <span class="status-badge status-approved">Disetujui</span>
                                                        @else
                                                            {{ $cell }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($header ?? []) }}" class="text-center py-4 text-muted">
                                                Data pendaftaran tidak ditemukan.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script Pencarian Nama --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();

                tableRows.forEach(function (row) {
                    const cells = row.querySelectorAll('td');
                    const nameCell = cells.length > 1 ? cells[1].innerText.toLowerCase() : '';

                    if (nameCell.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
