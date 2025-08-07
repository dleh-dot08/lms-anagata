<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Cek Status Pendaftaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status-badge {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .status-waiting {
            background-color: #ffc107;
            color: #fff;
        }
        .status-approved {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container my-5 pt-5">
    <nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center mx-lg-5">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img
                    src="{{ asset('koding_ka25/logo_all.png') }}"
                    alt="Anagata Academy Logo"
                />
            </a>

            <div class="d-none d-lg-flex mx-auto">
                <ul class="navbar-nav gap-4">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link fw-semibold text-dark"
                            href="https://ruanganagata.id/faq"
                            >FAQ</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link fw-semibold text-dark"
                            href="https://ruanganagata.id"
                            >LMS-RuangAnagata</a
                        >
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="/kka-paud/cek-pendaftaran">
                            Cek Status Pendaftaran
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pembayaran
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Invoice</a></li>
                            <li><a class="dropdown-item" href="#">Receipt</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="#">Upload Bukti Pembayaran</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h2 class="fw-bold mb-0">Cek Status Pendaftaran Guru</h2>
                    </div>
                    <div class="card-body p-4">
                        @if(isset($error_message))
                            <div class="alert alert-danger text-center" role="alert">
                                {{ $error_message }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        @if(isset($header) && !empty($header))
                                            @foreach($header as $column)
                                                <th>{{ $column }}</th>
                                            @endforeach
                                        @else
                                            <th>Tidak ada header</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($data) && count($data) > 0)
                                        @foreach($data as $row)
                                            <tr>
                                                @foreach($row as $key => $cell)
                                                    <td>
                                                        @if(is_string($cell) && strtolower($cell) == 'menunggu')
                                                            <span class="status-badge status-waiting">Menunggu</span>
                                                        @elseif(is_string($cell) && strtolower($cell) == 'disetujui')
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
                                            <td colspan="{{ count($header ?? []) }}" class="text-center py-4">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>