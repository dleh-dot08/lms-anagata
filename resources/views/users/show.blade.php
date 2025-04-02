@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Detail Pengguna</h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Nama: {{ $user->name }}</h5>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->role->name }}</p>
                <p><strong>Foto Diri:</strong> <img src="{{ asset('storage/' . $user->foto_diri) }}" alt="Foto Diri" class="img-thumbnail" style="max-width: 150px;"></p>
                <p><strong>Tanggal Lahir:</strong> {{ $user->tanggal_lahir }}</p>
                <p><strong>Alamat Tempat Tinggal:</strong> {{ $user->alamat_tempat_tinggal }}</p>
                <p><strong>Instansi:</strong> {{ $user->instansi }}</p>
                <p><strong>Divisi:</strong> {{ $user->divisi }}</p>
                <p><strong>No Telepon:</strong> {{ $user->no_telepon }}</p>
                <p><strong>Tanggal Bergabung:</strong> {{ $user->tanggal_bergabung }}</p>
                <p><strong>Surat Tugas:</strong> {{ $user->surat_tugas }}</p>

                <div class="mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Kembali ke Daftar Pengguna</a>
                </div>
            </div>
        </div>
    </div>
@endsection
