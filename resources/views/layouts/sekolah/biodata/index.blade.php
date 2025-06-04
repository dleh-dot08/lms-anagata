@extends('layouts.sekolah.template')

@section('content')
<div class="container py-4 px-3 px-md-4"> {{-- Sesuaikan padding horizontal untuk mobile --}}
    <div class="card shadow-sm p-3 p-md-4 p-lg-5"> {{-- Sesuaikan padding di berbagai ukuran layar --}}
        <h3 class="fw-bold text-center mb-4 text-primary fs-4 fs-md-3">Biodata Sekolah</h3> {{-- Ukuran font responsif dan warna --}}

        <div class="row g-4 mb-4 align-items-center"> {{-- Gunakan g-4 untuk jarak antar kolom dan align-items-center --}}
            <div class="col-lg-8 col-md-12"> {{-- Ambil lebar penuh di md dan sm, 2/3 di lg --}}
                <div class="card p-3 p-md-4 border-0 shadow-sm h-100"> {{-- Padding responsif dan tinggi 100% --}}
                    <h5 class="fw-bold text-primary mb-3 fs-5 fs-md-4">Informasi Akun PIC</h5> {{-- Ukuran font responsif --}}
                    <div class="table-responsive"> {{-- Penting untuk tabel agar responsif di layar kecil --}}
                        <table class="table table-borderless table-sm mb-0"> {{-- Gunakan table-borderless dan table-sm --}}
                            <tr>
                                <th class="text-nowrap" style="width: 150px;">Nama PIC</th>
                                <td>: {{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Email</th>
                                <td>: {{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">No Telepon PIC</th>
                                <td>: {{ $user->no_telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Status</th>
                                <td>
                                    @if ($biodata)
                                        : <span class="badge bg-{{ $biodata->status == 'aktif' ? 'success' : ($biodata->status == 'cuti' ? 'warning' : 'danger') }} py-2 px-3 rounded-pill">
                                            {{ ucfirst($biodata->status) }}
                                        </span>
                                    @else
                                        : <span class="badge bg-secondary py-2 px-3 rounded-pill">Belum Diisi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Nama Sekolah</th> {{-- Ubah label --}}
                                <td>: {{ $user->sekolah->nama_sekolah ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap align-top">Alamat Sekolah</th> {{-- align-top untuk alamat panjang --}}
                                <td>: {{ $user->sekolah->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 d-flex align-items-center justify-content-center"> {{-- Ambil lebar penuh di md dan sm, 1/3 di lg --}}
                <div class="card p-3 p-md-4 border-0 shadow-sm text-center w-100 h-100"> {{-- Lebar dan tinggi 100% --}}
                    <h5 class="fw-bold text-primary mb-3 fs-5 fs-md-4">Foto Profil PIC</h5> {{-- Ukuran font responsif --}}
                    <div class="d-flex justify-content-center align-items-center flex-grow-1"> {{-- Untuk centering gambar vertikal --}}
                        <img 
                            src="{{ $biodata && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('assets/img/elements/default-avatar.png') }}" 
                            alt="Foto User" 
                            class="img-fluid rounded-circle border border-primary shadow-sm" {{-- Tambah border-primary dan shadow-sm --}}
                            style="width: 160px; height: 160px; object-fit: cover;"> {{-- Ukuran sedikit lebih besar --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center my-4"> {{-- Margin vertikal responsif --}}
            <a href="{{ route('biodata.edit', Auth::id()) }}" class="btn btn-primary px-5 py-2 fw-semibold shadow-sm"> {{-- Padding, font-weight, dan shadow --}}
                <i class="bx bx-edit me-2"></i> Edit Biodata
            </a>
        </div>

        <div class="mt-4">
            <div class="card p-3 p-md-4 border-0 shadow-sm"> {{-- Padding responsif --}}
                <h5 class="fw-bold text-primary mb-3 fs-5 fs-md-4">Detail Biodata PIC</h5> {{-- Ukuran font responsif --}}
                <div class="table-responsive"> {{-- Penting untuk tabel agar responsif di layar kecil --}}
                    <table class="table table-borderless table-sm mb-0"> {{-- Gunakan table-borderless dan table-sm --}}
                        <tr>
                            <th class="text-nowrap" style="width: 150px;">NIP</th>
                            <td>: {{ $biodata->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Jenis Kelamin</th>
                            <td>: {{ $user->jenis_kelamin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Tempat, Tanggal Lahir</th>
                            <td>: {{ $biodata->tempat_lahir ?? '-' }}, {{ $biodata->tanggal_lahir ? \Carbon\Carbon::parse($biodata->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</td> {{-- Format tanggal dengan Carbon --}}
                        </tr>
                        <tr>
                            <th class="text-nowrap align-top">Alamat Domisili</th> {{-- Ubah label dan align-top --}}
                            <td>: {{ $biodata->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Instansi</th>
                            <td>: {{ $user->instansi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Jenjang</th>
                            <td>: {{ $user->jenjang->nama_jenjang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-nowrap">Tanggal Bergabung</th>
                            <td>: {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') }}</td> {{-- Format tanggal dengan Carbon --}}
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Styles --}}
<style>
    .card {
        border-radius: 12px;
    }
    .img-fluid.rounded-circle {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .img-fluid.rounded-circle:hover {
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.4) !important; /* Efek hover pada foto */
    }
    .table th {
        font-weight: 600; /* Sedikit lebih tebal untuk header tabel */
        color: #333; /* Warna teks yang lebih gelap */
    }
    .table td {
        color: #555; /* Warna teks isi tabel */
    }
    .badge {
        font-size: 0.85em; /* Ukuran badge sedikit lebih kecil */
        font-weight: 600;
    }
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
    }

    /* Media queries untuk responsivitas */
    @media (max-width: 767.98px) { /* Untuk perangkat di bawah 768px (MD breakpoint) */
        .px-md-4, .p-md-4 {
            padding-left: 1.5rem !important;
            padding-right: 1.5rem !important;
        }
        .fs-4 { font-size: 1.5rem !important; } /* Ukuran judul lebih kecil di mobile */
        .fs-5 { font-size: 1.15rem !important; }
        .fs-md-3 { font-size: 1.75rem !important; } /* Ukuran judul di md ke atas */
        .fs-md-4 { font-size: 1.35rem !important; } /* Ukuran sub-judul di md ke atas */

        .text-center.text-md-start {
            text-align: center !important; /* Pastikan rata tengah di mobile */
        }
        .mb-md-0 {
            margin-bottom: 1rem !important; /* Tambah jarak di mobile untuk elemen yang menumpuk */
        }
        .align-items-md-start {
            align-items: center !important; /* Tengah elemen di mobile */
        }
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .w-100 { /* Pastikan elemen mengambil lebar penuh di mobile */
            width: 100% !important;
        }
    }
</style>
@endsection