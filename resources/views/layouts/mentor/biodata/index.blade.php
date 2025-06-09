@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm p-lg-4 p-3">
        <h3 class="fw-bold text-center mb-4 text-primary">Biodata Mentor</h3>

        <div class="row g-4 mb-4 align-items-center">
            {{-- Informasi Akun --}}
            <div class="col-md-8">
                <div class="card p-4 h-100 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary mb-3">Informasi Akun</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 150px;">Nama</th>
                                    <td>: {{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>: {{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>No Telepon</th>
                                    <td>: {{ $user->no_telepon ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>: {{ $user->jabatan->nama_jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Divisi</th>
                                    <td>: {{ $user->divisi->nama_divisi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($biodata)
                                            : <span class="badge bg-{{ $biodata->status == 'aktif' ? 'success' : ($biodata->status == 'cuti' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($biodata->status) }}
                                            </span>
                                        @else
                                            : <span class="badge bg-secondary">Belum Diisi</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Foto Profil --}}
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm text-center d-flex flex-column justify-content-center align-items-center">
                    <h5 class="fw-bold text-primary mb-3">Foto Profil</h5>
                    <img
                        src="{{ $biodata && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('assets/img/elements/default-avatar.png') }}"
                        alt="Foto User"
                        class="img-fluid rounded-circle border border-2 shadow-sm"
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>
            </div>
        </div>

        {{-- Biodata Detail --}}
        <div class="row">
            <div class="col-12">
                <div class="card p-4 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary mb-3">Informasi Biodata Detail</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 150px;">NIP</th>
                                    <td>: {{ $biodata->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap</th>
                                    <td>: {{ $biodata->nama_lengkap ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>: {{ $biodata->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <td>
                                        :
                                        {{ $biodata?->tempat_lahir ?? '-' }},
                                        {{ $biodata?->tanggal_lahir ? \Carbon\Carbon::parse($biodata->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $biodata->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Bergabung</th>
                                    <td>: {{ \Carbon\Carbon::parse($user->tanggal_bergabung)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Foto KTP</th>
                                    <td>
                                        @if ($biodata?->data_ktp)
                                            <img src="{{ asset('storage/' . $biodata->data_ktp) }}" alt="Foto KTP" class="img-fluid rounded shadow-sm" style="max-width: 150px;">
                                            <button class="btn btn-outline-primary btn-sm ms-2 mt-2 mt-sm-0" onclick="printKtp('{{ $biodata->data_ktp }}')">
                                                <i class="bx bx-printer me-1"></i> Cetak KTP
                                            </button>
                                        @else
                                            <span class="text-danger">Belum diupload</span>
                                            <button class="btn btn-outline-secondary btn-sm ms-2 mt-2 mt-sm-0" disabled>
                                                <i class="bx bx-file-blank me-1"></i> Tidak Ada
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanda Tangan</th>
                                    <td>
                                        @if ($biodata?->data_ttd)
                                            <img src="{{ asset('storage/' . $biodata->data_ttd) }}" alt="Tanda Tangan" class="img-fluid rounded shadow-sm" style="max-width: 150px;">
                                        @else
                                            <span class="text-danger">Belum diupload</span>
                                            <button class="btn btn-outline-secondary btn-sm ms-2 mt-2 mt-sm-0" disabled>
                                                <i class="bx bx-pencil me-1"></i> Tidak Ada
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Edit --}}
        <div class="text-center mt-4">
            <a href="{{ route('biodata.edit', Auth::id()) }}" class="btn btn-primary px-4 py-2 rounded-pill shadow">
                <i class="bx bx-edit me-1"></i> Edit Data
            </a>
        </div>
    </div>
</div>

<script>
    function printKtp(data_ktp_path) {
        window.open("{{ asset('storage') }}/" + data_ktp_path.replace('app/public/', ''), '_blank');
    }
</script>
@endsection
