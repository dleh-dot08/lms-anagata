@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Edit Biodata</h3>
        <form action="{{ route('biodata.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informasi Akun -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Akun</h5>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="{{ $user->no_telepon }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ $user->alamat }}</textarea>
                </div>
            </div>

            <!-- Informasi Biodata -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ $biodata?->nama_lengkap }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ $biodata?->nik }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ $biodata?->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $biodata?->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tempat, Tanggal Lahir</label>
                    <div class="d-flex">
                        <input type="text" name="tempat_lahir" class="form-control me-2" value="{{ $biodata?->tempat_lahir }}">
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $biodata?->tanggal_lahir }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ $biodata?->no_hp }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control" value="{{ $biodata?->instansi }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang_id" class="form-select">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach($jenjangList as $jenjang)
                            <option value="{{ $jenjang->id }}" {{ $biodata?->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" value="{{ $biodata?->pekerjaan }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pendidikan Terakhir</label>
                    <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $biodata?->pendidikan_terakhir }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Media Sosial (Instagram/Facebook/LinkedIn)</label>
                    <input type="text" name="media_sosial" class="form-control" value="{{ $biodata?->media_sosial }}">
                </div>
            </div>

            <!-- Upload Berkas -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Upload Berkas</h5>
                <div class="mb-3">
                    <label class="form-label">Foto KTP</label>
                    <input type="file" name="foto_ktp" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanda Tangan</label>
                    <input type="file" name="data_ttd" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-control">
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-center">
                <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                <a href="{{ route('biodata.index') }}" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
