@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title">Detail Pengguna</h4>
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Foto Diri</th>
                        <td>
                            @if($user->foto_diri)
                                <img src="{{ asset('storage/' . $user->foto_diri) }}" alt="Foto Diri" width="150">
                            @else
                                Tidak ada foto.
                            @endif
                        </td>
                    </tr>
                    <tr><th>Nama</th><td>{{ $user->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                    <tr><th>Role</th><td>{{ $user->role->name }}</td></tr>
                    <tr><th>Tempat & Tanggal Lahir</th><td>{{ $user->tempat_lahir }}, {{ $user->tanggal_lahir }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $user->alamat_tempat_tinggal }}</td></tr>
                    <tr><th>Instansi</th><td>{{ $user->instansi }}</td></tr>
                    <tr><th>Jabatan</th><td>{{ $user->jabatan }}</td></tr>
                    <tr><th>Bidang Pengajaran</th><td>{{ $user->bidang_pengajaran }}</td></tr>
                    <tr><th>Divisi</th><td>{{ $user->divisi }}</td></tr>
                    <tr><th>No. Telepon</th><td>{{ $user->no_telepon }}</td></tr>
                    <tr><th>Tanggal Bergabung</th><td>{{ $user->tanggal_bergabung }}</td></tr>
                    <tr><th>Surat Tugas</th><td>{{ $user->surat_tugas }}</td></tr>
                </table>
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">Edit Pengguna</a>
            </div>
        </div>
    </div>
@endsection
