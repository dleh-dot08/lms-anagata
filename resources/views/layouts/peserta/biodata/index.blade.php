@extends('layouts.peserta.template')

@section('content')
<div class="container py-4"> <div class="card shadow-lg p-md-5 p-3 animated fadeIn"> <h3 class="fw-bold text-center mb-4 text-primary">Profil Peserta</h3>
        <p class="text-center text-muted mb-5">Lihat dan kelola informasi akun serta biodata Anda.</p>

        <div class="row g-4 mb-5"> <div class="col-12 col-md-4 d-flex justify-content-center align-items-center order-md-2">
                <div class="card p-3 border-0 bg-light text-center w-100 h-100 d-flex flex-column justify-content-center align-items-center"> <img
                        src="{{ $biodata && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('assets/img/elements/default-avatar.png') }}"
                        alt="Foto Profil Pengguna"
                        class="img-fluid rounded-circle border border-3 border-primary shadow-sm mb-3"
                        style="width: 120px; height: 120px; object-fit: cover; aspect-ratio: 1/1;"
                    >
                    <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small">{{ $user->email }}</p>
                </div>
            </div>

            <div class="col-12 col-md-8 order-md-1">
                <div class="card p-4 shadow-sm h-100"> <h5 class="fw-bold text-primary mb-3"><i class="bx bx-user me-2"></i> Informasi Akun</h5>
                    <div class="row g-2"> <div class="col-6"><strong>Nama Lengkap</strong></div>
                        <div class="col-6 text-break">{{ $user->name }}</div>

                        <div class="col-6"><strong>Email</strong></div>
                        <div class="col-6 text-break">{{ $user->email }}</div>

                        <div class="col-6"><strong>No. WhatsApp</strong></div>
                        <div class="col-6 text-break">{{ $user->no_telepon ?? '-' }}</div>

                        <div class="col-6"><strong>Status</strong></div>
                        <div class="col-6">
                            @if ($biodata)
                                <span class="badge bg-{{ $biodata->status == 'aktif' ? 'success' : ($biodata->status == 'cuti' ? 'warning' : 'danger') }} py-2 px-3 fw-semibold">
                                    {{ ucfirst($biodata->status) }}
                                </span>
                            @else
                                <span class="badge bg-secondary py-2 px-3 fw-semibold">Belum Diisi</span>
                            @endif
                        </div>

                        <div class="col-6"><strong>Sekolah</strong></div>
                        <div class="col-6 text-break">{{ $user->sekolah->nama_sekolah ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5"> <a href="{{ route('biodata.edit', Auth::id()) }}" class="btn btn-primary btn-lg px-5 shadow-sm animated pulse"> <i class="bx bx-edit me-2"></i> Edit Profil
            </a>
        </div>

        <div class="card p-4 shadow-sm animated fadeIn"> <h5 class="fw-bold text-primary mb-3"><i class="bx bx-info-circle me-2"></i> Detail Biodata</h5>
            <div class="row g-2">
                <div class="col-6"><strong>Jenis Kelamin</strong></div>
                <div class="col-6 text-break">{{ $user->jenis_kelamin ?? '-' }}</div>

                <div class="col-6"><strong>Tempat, Tanggal Lahir</strong></div>
                <div class="col-6 text-break">
                    {{ $biodata->tempat_lahir ?? '-' }},
                    {{ $biodata->tanggal_lahir ? \Carbon\Carbon::parse($biodata->tanggal_lahir)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}
                </div>

                <div class="col-6"><strong>Alamat</strong></div>
                <div class="col-6 text-break">{{ $biodata->alamat ?? '-' }}</div>

                <div class="col-6"><strong>Jenjang</strong></div>
                <div class="col-6 text-break">{{ $user->jenjang->nama_jenjang ?? '-' }}</div>

                <div class="col-6"><strong>Kelas</strong></div>
                <div class="col-6 text-break">{{ $user->kelas->nama ?? '-' }}</div>

                <div class="col-6"><strong>Media Sosial</strong></div>
                <div class="col-6 text-break">{{ $user->media_sosial ?? '-' }}</div>

                <div class="col-6"><strong>Tanggal Bergabung</strong></div>
                <div class="col-6 text-break">{{ \Carbon\Carbon::parse($user->created_at)->locale('id')->isoFormat('D MMMM YYYY') }}</div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    /* Animasi Fade In */
    .animated.fadeIn {
        animation: fadeIn 0.8s ease-out forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Animasi Pulse untuk tombol */
    .animated.pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    /* Override gaya tabel default agar lebih responsif untuk daftar detail */
    .row.g-2 > div {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .row.g-2 > div:nth-child(even) { /* Untuk nilai, agar terlihat berbeda dari label */
        font-weight: normal;
        color: #555;
    }
    .row.g-2 > div:nth-child(odd) { /* Untuk label, agar tebal */
        font-weight: 600;
        color: #333;
    }

    /* Pastikan gambar profil tetap bulat di semua kondisi */
    .rounded-circle {
        border-radius: 50% !important;
    }

    /* Tambahan untuk text-break pada nilai yang panjang */
    .text-break {
        word-wrap: break-word;
        white-space: normal;
    }

    /* Responsive adjustments for the info layout inside cards */
    @media (max-width: 575.98px) { /* Extra small devices (phones, 320px and up) */
        .card.p-md-5 {
            padding: 1rem !important; /* Kurangi padding di mobile */
        }
        .row.g-2 > div.col-6 {
            flex: 0 0 100%; /* Buat setiap label dan nilai menjadi baris penuh */
            max-width: 100%;
        }
        .row.g-2 > div.col-6:nth-child(odd) { /* Label */
            padding-bottom: 0; /* Kurangi padding antara label dan nilai */
        }
        .row.g-2 > div.col-6:nth-child(even) { /* Nilai */
            padding-top: 0; /* Kurangi padding antara label dan nilai */
            margin-bottom: 0.5rem; /* Tambah jarak ke item berikutnya */
        }
        .row.g-2 > div.col-6:last-child {
            margin-bottom: 0; /* Hapus margin bawah di item terakhir */
        }
    }
</style>
@endpush
@endsection