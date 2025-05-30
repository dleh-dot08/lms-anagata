@extends('layouts.karyawan.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Edit Biodata</h3>

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Perhatian!</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <form action="{{ route('biodata.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mt-4">
                <!-- Kolom User -->
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Informasi User</h5>
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
            </div>

            <!-- Kolom Biodata -->
            <div class="mt-4">
                    <div class="card p-3 border-0 shadow-sm">
                        <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                        <div class="mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" value="{{ $biodata?->nip }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ $biodata?->nik }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tempat, Tanggal Lahir</label>
                            <div class="d-flex">
                                <input type="text" name="tempat_lahir" class="form-control me-2" value="{{ $biodata?->tempat_lahir }}">
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ $biodata?->tanggal_lahir }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" {{ $user?->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $user?->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pendidikan terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $user?->pendidikan_terakhir }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Media Sosial</label>
                            <input type="text" name="media_sosial" class="form-control" value="{{ $user?->media_sosial }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ $biodata?->no_hp }}">
                        </div>
                    </div>
            </div>

            <!-- Upload Berkas -->
            <div class="mt-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Upload Berkas</h5>
                    <div class="mb-3">
                        <label class="form-label">Foto KTP</label>
                        <input type="file" name="foto_ktp" class="form-control">
                        @if (!$biodata?->data_ktp)
                            <span class="text-danger">Belum diupload</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanda Tangan</label>
                        <input type="file" name="data_ttd" class="form-control">
                        @if (!$biodata?->data_ttd)
                            <span class="text-danger">Belum diupload</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto" class="form-control">
                        @if (!$biodata?->foto)
                            <span class="text-danger">Belum diupload</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                <a href="{{ route('biodata.index') }}" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
