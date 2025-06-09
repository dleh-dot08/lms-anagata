@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <div class="card shadow-sm p-4">
        <h3 class="fw-bold text-center mb-4">Edit Biodata</h3>

        {{-- Notifikasi --}}
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

        {{-- Form Biodata --}}
        <form action="{{ route('biodata.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Informasi User --}}
            <div class="row">
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Informasi User</h5>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap + Gelar</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Aktif</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                        <input type="hidden" name="email" value="{{ $user->email }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No WhatsApp Aktif</label>
                        <input type="text" name="no_telepon" class="form-control" value="{{ $user->no_telepon }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control">{{ $user->alamat }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Informasi Biodata --}}
            <div class="row mt-4">
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
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $biodata?->no_hp }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ijazah Terakhir (PDF / JPG)</label>
                        <input type="file" name="ijazah" class="form-control" accept="application/pdf,image/*">
                        @if (!$biodata?->ijazah)
                            <span class="text-danger d-block mt-1">Belum diupload</span>
                        @endif
                    </div>

                    <h5 class="fw-bold text-primary mt-4">Informasi Bank</h5>

                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" name="nama_bank" class="form-control" value="{{ $biodata?->nama_bank }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="nama_pemilik_bank" class="form-control" value="{{ $biodata?->nama_pemilik_bank }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="no_rekening" class="form-control" value="{{ $biodata?->no_rekening }}">
                    </div>
                </div>
            </div>

            {{-- Upload Berkas --}}
            <div class="mt-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h5 class="fw-bold text-primary">Upload Berkas</h5>

                    <div class="mb-3">
                        <label class="form-label">Foto KTP (Maks. 5MB)</label>
                        <input type="file" name="foto_ktp" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                        @if (!$biodata?->data_ktp)
                            <span class="text-danger d-block mt-1">Belum diupload</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanda Tangan (Maks. 5MB)</label>
                        <input type="file" name="data_ttd" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                        @if (!$biodata?->data_ttd)
                            <span class="text-danger d-block mt-1">Belum diupload</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (Maks. 5MB)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 5MB</small>
                        @if (!$biodata?->foto)
                            <span class="text-danger d-block mt-1">Belum diupload</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto 3x4 (Maks. 5MB)</label>
                        <input type="file" name="foto_3x4" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 5MB</small>
                        @if (!$biodata?->foto_3x4)
                            <span class="text-danger d-block mt-1">Belum diupload</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Sertifikat Terkait (jika ada)</label>
                        <input type="file" name="sertifikat[]" class="form-control" accept="application/pdf,image/*" multiple>
                        <small class="text-muted">Boleh lebih dari satu file. Format: PDF, JPG, PNG</small>
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
                <a href="{{ route('biodata.index') }}" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
