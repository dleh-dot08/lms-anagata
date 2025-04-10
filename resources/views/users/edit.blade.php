@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title">Edit Pengguna</h4>
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Role</label>
                        <select name="role_id" class="form-control">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Foto Diri</label>
                        <input type="file" name="foto_diri" class="form-control">
                        @if($user->foto_diri)
                            <img src="{{ asset('storage/' . $user->foto_diri) }}" alt="Foto Diri" width="100" class="mt-2">
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ $user->tanggal_lahir }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ $user->tempat_lahir }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Alamat Tempat Tinggal</label>
                        <textarea name="alamat_tempat_tinggal" class="form-control">{{ $user->alamat_tempat_tinggal }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Instansi</label>
                        <textarea name="instansi" class="form-control">{{ $user->instansi }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Jenjang</label>
                        <select name="jenjang_id" class="form-control">
                            @foreach ($jenjangs as $jenjang)
                                <option value="{{ $jenjang->id }}" {{ $user->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                                    {{ $jenjang->nama_jenjang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" value="{{ $user->jabatan }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Bidang Pengajaran</label>
                        <textarea name="bidang_pengajaran" class="form-control">{{ $user->bidang_pengajaran }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label>Divisi</label>
                        <input type="text" name="divisi" value="{{ $user->divisi }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telepon" value="{{ $user->no_telepon }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Tanggal Bergabung</label>
                        <input type="date" name="tanggal_bergabung" value="{{ $user->tanggal_bergabung }}" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label>Surat Tugas</label>
                        <input type="text" name="surat_tugas" value="{{ $user->surat_tugas }}" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
@endsection
