@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Biodata Peserta</h3>
        <div class="row">
            <!-- Informasi User -->
            <div class="col-md-8">
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Informasi Akun</h5>
                    <table class="table">
                        <tr><th>Nama</th><td>: {{ $user->name }}</td></tr>
                        <tr><th>Email</th><td>: {{ $user->email }}</td></tr>
                        <tr><th>No Hanphone</th><td>: {{ $user->no_telepon ?? '-' }}</td></tr>
                        <tr><th>Status</th>
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
                    </table>
                </div>
            </div>

            <!-- Foto -->
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <div class="card p-3 border-0 shadow-sm text-center">
                    <h5 class="fw-bold text-primary">Foto Profil</h5>
                    <img 
                        src="{{ $biodata && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('assets/img/elements/default-avatar.png') }}" 
                        alt="Foto User" 
                        class="img-fluid rounded-circle border shadow" 
                        style="width: 150px; height: 150px; object-fit: cover;">
                </div>
            </div>
        </div>

        <!-- Tombol Edit -->
        <div class="text-center mt-4">
            <a href="{{ route('biodata.edit', Auth::id()) }}" class="btn btn-primary px-4">
                 <i class="bx bx-edit"></i> Edit Data
            </a>
        </div>

        <div class="p-3 mt-4">
            <div class="card p-3 border-0 shadow-sm">
                <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                <table class="table">
                    <tr><th>NIK</th><td>: {{ $biodata->nik ?? '-' }}</td></tr>
                    <tr><th>Jenis Kelamin</th><td>: {{ $user->jenis_kelamin ?? '-' }}</td></tr>
                    <tr>
                        <th>Tempat, Tanggal Lahir</th>
                        <td>: {{ $biodata->tempat_lahir ?? '-' }}, {{ $biodata->tanggal_lahir ? date('d M Y', strtotime($biodata->tanggal_lahir)) : '-' }}</td>
                    </tr>
                    <tr><th>Alamat</th><td>: {{ $biodata->alamat ?? '-' }}</td></tr>
                    <tr><th>Instansi</th><td>: {{ $user->instansi ?? '-' }}</td></tr>
                    <tr><th>Pekerjaan</th><td>: {{ $user->pekerjaan ?? '-' }}</td></tr>
                    <tr><th>Jenjang</th><td>: {{ $user->jenjang->nama_jenjang ?? '-' }}</td></tr>
                    <tr><th>Bidang Pengajaran</th><td>: {{ $user->bidang_pengajaran ?? '-' }}</td></tr>
                    <tr><th>Pendidikan Terakhir</th><td>: {{ $user->pendidikan_terakhir ?? '-' }}</td></tr>
                    <tr><th>Sosial Media</th><td>: {{ $user->media_sosial ?? '-' }}</td></tr>
                    <tr><th>Tanggal Bergabung</th><td>: {{ date('d M Y', strtotime($user->tanggal_bergabung)) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
