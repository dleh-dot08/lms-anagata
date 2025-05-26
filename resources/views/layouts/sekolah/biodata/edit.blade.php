@extends('layouts.sekolah.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Edit Biodata Sekolah</h3>

        @if(session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('biodata.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informasi Akun -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Akun</h5>
                <div class="mb-3">
                    <label class="form-label">Nama PIC</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No Telepon PIC</label>
                    <input type="text" name="no_telepon" class="form-control" value="{{ $user->no_telepon }}">
                    <small class="text-muted">Gunakan Nomor Whatsapp Aktif</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">PIC Sekolah</label>
                    <input type="text" class="form-control" value="{{ $user->sekolah->nama_sekolah ?? '-' }}" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Sekolah</label>
                    <textarea class="form-control" readonly disabled>{{ $user->sekolah->alamat ?? '-' }}</textarea>
                </div>
            </div>

            <!-- Informasi Biodata -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Informasi Biodata</h5>
                <div class="mb-3">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control" value="{{ $biodata?->nip }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" {{ $user->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $user->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ $biodata?->tempat_lahir }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $biodata?->tanggal_lahir }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ $biodata?->alamat }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control" value="{{ $user->instansi }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenjang</label>
                    <select name="jenjang_id" class="form-select">
                        <option value="">-- Pilih Jenjang --</option>
                        @foreach($jenjangs as $jenjang)
                            <option value="{{ $jenjang->id }}" {{ $user->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama_jenjang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Upload Berkas -->
            <div class="card p-3 border-0 shadow-sm mb-4">
                <h5 class="fw-bold text-primary">Upload Berkas</h5>
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto" class="form-control">
                    <small class="text-muted">Type File JPEG/PNG ukuran max. 2MB</small>
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