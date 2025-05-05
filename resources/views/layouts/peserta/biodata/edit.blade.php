@extends('layouts.peserta.template')

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

            <!-- Informasi Akun -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Akun</h5>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No Handphone</label>
                    <input type="text" name="no_telepon" class="form-control" 
                        value="{{ $user->no_telepon }}">
                    <small class="text-muted">Gunakan Nomor Whatsapp Aktif</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat KTP</label>
                    <textarea name="alamat" class="form-control">{{ $biodata?->alamat }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Domisili</label>
                    <textarea name="alamat_tempat_tinggal" class="form-control">{{ $user?->alamat_tempat_tinggal }}</textarea>
                    <small class="text-muted">jika sama dengan Alamat KTP maka isi sesuai KTP</small>
                </div>
            </div>

            <!-- Informasi Biodata -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                <div class="mb-3">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ $biodata?->nik }}">
                    <small class="text-muted">Jika Peserta memiliki KTP</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ $user?->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $user?->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ $biodata?->tempat_lahir }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ $biodata?->tanggal_lahir }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control" value="{{ $user?->instansi }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" class="form-control" value="{{ $user?->pekerjaan }}">
                    <small class="text-muted">Jika Tenaga Pendidik maka masukan juga rumpun ilmu / bidang pengajaran</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang_id" class="form-select">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach($jenjangs as $jenjang)
                            <option value="{{ $jenjang->id }}" {{ $user?->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama_jenjang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bidang Keahlian</label>
                    <input type="text" name="bidang_pengajaran" class="form-control" value="{{ $user?->bidang_pengajaran}}">
                    <small class="text-muted">Tidak diwajibkan untuk Siswa</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pendidikan Terakhir</label>
                    <select name="pendidikan_terakhir" class="form-control">
                        <option value="">-- Pilih Pendidikan --</option>
                        <option value="SD" {{ $user?->pendidikan_terakhir == 'SD' ? 'selected' : '' }}>SD</option>
                        <option value="SMP" {{ $user?->pendidikan_terakhir == 'SMP' ? 'selected' : '' }}>SMP</option>
                        <option value="SMA" {{ $user?->pendidikan_terakhir == 'SMA' ? 'selected' : '' }}>SMA</option>
                        <option value="SMK" {{ $user?->pendidikan_terakhir == 'SMK' ? 'selected' : '' }}>SMK</option>
                        <option value="D1" {{ $user?->pendidikan_terakhir == 'D1' ? 'selected' : '' }}>D1</option>
                        <option value="D2" {{ $user?->pendidikan_terakhir == 'D2' ? 'selected' : '' }}>D2</option>
                        <option value="D3" {{ $user?->pendidikan_terakhir == 'D3' ? 'selected' : '' }}>D3</option>
                        <option value="D4" {{ $user?->pendidikan_terakhir == 'D4' ? 'selected' : '' }}>D4</option>
                        <option value="S1" {{ $user?->pendidikan_terakhir == 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ $user?->pendidikan_terakhir == 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ $user?->pendidikan_terakhir == 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Media Sosial (Instagram/Facebook/LinkedIn)</label>
                    <input type="text" name="media_sosial" class="form-control" value="{{ $user?->media_sosial }}">
                </div>
            </div>

            <!-- Upload Berkas -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Upload Berkas</h5>
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-control">
                    <small class="text-muted">Type File JPEG/PNG ukuran max. 2MB </small>
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
