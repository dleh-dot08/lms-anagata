@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Biodata Mentor</h3>
        <!-- Kolom User -->
        <div class="row">
            <!-- Kolom Kiri: Informasi User -->
            <div class="col-md-8">
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Informasi User</h5>
                    <table class="table">
                        <tr><th>Nama</th><td>: {{ $user->name }}</td></tr>
                        <tr><th>Email</th><td>: {{ $user->email }}</td></tr>
                        <tr><th>No Telepon</th><td>: {{ $user->no_telepon ?? '-' }}</td></tr>
                        <tr><th>Jabatan</th><td>: {{ $user->jabatan->nama_jabatan ?? '-' }}</td></tr>
                        <tr><th>Divisi</th><td>: {{ $user->divisi->nama_divisi ?? '-' }}</td></tr>
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

                    </table>
                </div>
            </div>

            <!-- Kolom Kanan: Foto User -->
            <div class="col-md-4 d-flex align-items-center justify-content-center">
                <div class="card p-3 border-0 shadow-sm text-center">
                    <h5 class="fw-bold text-primary">Foto User</h5>
                    <img 
                    src="{{ $biodata && $biodata->foto ? asset('storage/' . $biodata->foto) : asset('assets/img/elements/default-avatar.png') }}" 
                    alt="Foto User" 
                    class="img-fluid rounded-circle border shadow" 
                    style="width: 150px; height: 150px; object-fit: cover;">

                </div>
            </div>
        </div>
        <!-- <br/> -->


        <!-- Tab Content -->
        <div class="p-3 mt-3">
            <!-- Tab Biodata -->
            <div class="tab-pane fade show active" id="biodata" role="tabpanel" aria-labelledby="biodata-tab">
                <div class="row">
                    <!-- Kolom Biodata -->
                        <div class="card p-3 border-0 shadow-sm">
                            <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                            <table class="table">
                                <tr><th>NIP</th><td>: {{ $biodata->nip ?? '-' }}</td></tr>
                                <tr><th>Nama Lengkap</th><td>: {{ $biodata->nama_lengkap ?? '-' }}</td></tr>
                                <tr><th>NIK</th><td>: {{ $biodata->nik ?? '-' }}</td></tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <td>
                                        : 
                                        {{ $biodata?->tempat_lahir ?? '-' }}, 
                                        {{ $biodata?->tanggal_lahir ? date('d M Y', strtotime($biodata->tanggal_lahir)) : '-' }}
                                    </td>
                                </tr>

                                <tr><th>No HP</th><td>: {{ $biodata->no_hp ?? '-' }}</td></tr>
                                <tr><th>Alamat</th><td>: {{ $biodata->alamat ?? '-' }}</td></tr>
                                <tr><th>Jabatan</th><td>: {{ $user->jabatan->nama_jabatan ?? '-' }}</td></tr>
                                <tr><th>Divisi</th><td>: {{ $user->divisi->nama_divisi ?? '-' }}</td></tr>
                                <tr><th>Tanggal Bergabung</th><td>: {{ date('d M Y', strtotime($user->tanggal_bergabung)) }}</td></tr>

                                <!-- Foto KTP -->
                                <tr>
                                    <th>Foto KTP</th>
                                    <td>
                                        @if ($biodata?->data_ktp)
                                            <img src="{{ asset('storage/app/public/' . $biodata->data_ktp) }}" alt="Foto KTP" width="100">
                                            <button class="btn btn-primary btn-sm" onclick="printKtp('{{ $biodata->data_ktp }}')">
                                                <i class="menu-icon tf-icons bx bx-printer"></i>
                                            </button>
                                            <script>
                                                function printKtp(data_ktp) {
                                                    window.open("{{ asset('storage/app/public/', '') }}/" + data_ktp, '_blank');
                                                }
                                            </script>
                                        @else
                                            <button class="btn btn-danger btn-sm">Belum diupload</button>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Tanda Tangan -->
                                <tr>
                                    <th>Tanda Tangan</th>
                                    <td>
                                        @if ($biodata?->data_ttd)
                                            <img src="{{ asset('storage/app/public/' . $biodata->data_ttd) }}" alt="Tanda Tangan" width="100">
                                        @else
                                            <button class="btn btn-danger btn-sm">Belum diupload</button>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                </div>

                <!-- Tombol Edit -->
                <div class="text-center mt-4">
                <a href="{{ route('biodata.edit', Auth::id()) }}" class="btn btn-primary px-4">
                    <i class="bx bx-edit"></i> Edit Data
                </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
