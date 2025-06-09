@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengguna
            </a>
            <h3 class="fw-bold mb-0">
                <i class="bi bi-person-badge text-primary me-2"></i>Detail Pengguna
            </h3>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-fill me-1"></i> Edit Pengguna
            </a>
            @if($user->deleted_at)
                <form action="{{ route('users.restore', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Aktifkan Pengguna
                    </button>
                </form>
            @else
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                    <i class="bi bi-trash-fill me-1"></i> Non-aktifkan Pengguna
                </button>
            @endif
        </div>
    </div>

    @if($user->deleted_at)
    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            Pengguna ini saat ini tidak aktif (dinonaktifkan pada {{ \Carbon\Carbon::parse($user->deleted_at)->format('d M Y, H:i') }})
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body text-center p-4">
                    <div class="position-relative mx-auto mb-4" style="width: 150px; height: 150px;">
                        @if($user->foto_diri)
                            <img src="{{ asset('storage/' . $user->foto_diri) }}" alt="Foto {{ $user->name }}"
                                class="rounded-circle object-fit-cover border" style="width: 150px; height: 150px;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border"
                                style="width: 150px; height: 150px;">
                                <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        @endif

                        <div class="position-absolute bottom-0 end-0">
                            <span class="badge rounded-pill bg-{{ $user->deleted_at ? 'danger' : 'success' }} p-2">
                                <i class="bi bi-circle-fill"></i> {{ $user->deleted_at ? 'Tidak Aktif' : 'Aktif' }}
                            </span>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center mb-3">
                        <span class="badge bg-primary px-3 py-2">
                            {{ $user->role->name }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                        <div class="text-center">
                            <h6 class="fw-bold mb-0">{{ $user->courses_count ?? 0 }}</h6>
                            <small class="text-muted">Kursus</small>
                        </div>
                        <div class="text-center">
                            <h6 class="fw-bold mb-0">{{ $user->tasks_count ?? 0 }}</h6>
                            <small class="text-muted">Tugas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-telephone-fill text-primary me-2"></i>Kontak
                    </h5>

                    <div class="mb-3">
                        <p class="text-muted mb-1 small">Email</p>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="text-muted mb-1 small">No. Telepon</p>
                        <p class="mb-0">
                            @if($user->no_telepon)
                                <a href="tel:{{ $user->no_telepon }}" class="text-decoration-none">
                                    {{ $user->no_telepon }}
                                </a>
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-muted mb-1 small">Alamat</p>
                        <p class="mb-0">
                            @if($user->alamat_tempat_tinggal)
                                {{ $user->alamat_tempat_tinggal }}
                            @else
                                <span class="text-muted">Tidak tersedia</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <ul class="nav nav-tabs" id="userDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-tab-pane"
                                type="button" role="tab" aria-controls="personal-tab-pane" aria-selected="true">
                                <i class="bi bi-person me-1"></i>Informasi Pribadi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional-tab-pane"
                                type="button" role="tab" aria-controls="professional-tab-pane" aria-selected="false">
                                <i class="bi bi-briefcase me-1"></i>Informasi Profesi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-tab-pane"
                                type="button" role="tab" aria-controls="account-tab-pane" aria-selected="false">
                                <i class="bi bi-gear me-1"></i>Informasi Akun
                            </button>
                        </li>
                        {{-- Tab Berkas Unggahan (hanya jika role_id adalah 2) --}}
                        @if($user->role_id == 2)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-tab-pane"
                                type="button" role="tab" aria-controls="documents-tab-pane" aria-selected="false">
                                <i class="bi bi-folder me-1"></i>Berkas Unggahan Mentor
                            </button>
                        </li>
                        @endif
                    </ul>

                    <div class="tab-content p-3" id="userDetailTabsContent">
                        <div class="tab-pane fade show active" id="personal-tab-pane" role="tabpanel" aria-labelledby="personal-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Nama Lengkap</label>
                                        <div class="border rounded-3 p-3 bg-light">{{ $user->name }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Tempat & Tanggal Lahir</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->tempat_lahir && $user->tanggal_lahir)
                                                {{ $user->tempat_lahir }}, {{ \Carbon\Carbon::parse($user->tanggal_lahir)->format('d F Y') }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Alamat Tempat Tinggal</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->alamat_tempat_tinggal)
                                                {{ $user->alamat_tempat_tinggal }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">No. Telepon</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->no_telepon)
                                                {{ $user->no_telepon }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="professional-tab-pane" role="tabpanel" aria-labelledby="professional-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Instansi</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->instansi)
                                                {{ $user->instansi }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Jenjang</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if(isset($user->jenjang->nama_jenjang))
                                                {{ $user->jenjang->nama_jenjang }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Jabatan</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->jabatan)
                                                {{ $user->jabatan }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Divisi</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->divisi)
                                                {{ $user->divisi }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Bidang Pengajaran</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->bidang_pengajaran)
                                                {{ $user->bidang_pengajaran }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Surat Tugas</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->surat_tugas)
                                                {{ $user->surat_tugas }}
                                                <a href="{{ asset('storage/' . $user->surat_tugas) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                    <i class="bi bi-file-earmark-text"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="account-tab-pane" role="tabpanel" aria-labelledby="account-tab" tabindex="0">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Email</label>
                                        <div class="border rounded-3 p-3 bg-light">{{ $user->email }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Role</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            <span class="badge bg-primary px-2 py-1">{{ $user->role->name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Tanggal Bergabung</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->tanggal_bergabung)
                                                {{ \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d F Y') }}
                                                <span class="text-muted ms-2">
                                                    ({{ \Carbon\Carbon::parse($user->tanggal_bergabung)->diffForHumans() }})
                                                </span>
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Status Akun</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            <span class="badge bg-{{ $user->deleted_at ? 'danger' : 'success' }}">
                                                {{ $user->deleted_at ? 'Tidak Aktif' : 'Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Konten Tab Berkas Unggahan (hanya jika role_id adalah 2) --}}
                        @if($user->role_id == 2)
                        <div class="tab-pane fade" id="documents-tab-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">
                            <div class="row g-4">
                                <h5 class="fw-bold text-primary">Berkas Unggahan Mentor</h5>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Ijazah Terakhir</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->ijazah)
                                                <a href="{{ asset('storage/' . $user->biodata->ijazah) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download me-1"></i> Unduh Ijazah
                                                </a>
                                                <small class="text-muted ms-2">{{ pathinfo($user->biodata->ijazah, PATHINFO_BASENAME) }}</small>
                                            @else
                                                <span class="text-muted">Belum diunggah</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Foto 3x4</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->foto_3x4)
                                                <a href="{{ asset('storage/' . $user->biodata->foto_3x4) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-camera me-1"></i> Lihat Foto 3x4
                                                </a>
                                                <small class="text-muted ms-2">{{ pathinfo($user->biodata->foto_3x4, PATHINFO_BASENAME) }}</small>
                                            @else
                                                <span class="text-muted">Belum diunggah</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Nama Bank</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->nama_bank)
                                                {{ $user->biodata->nama_bank }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Nama Pemilik Rekening</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->nama_pemilik_bank)
                                                {{ $user->biodata->nama_pemilik_bank }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Nomor Rekening</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->no_rekening)
                                                {{ $user->biodata->no_rekening }}
                                            @else
                                                <span class="text-muted">Tidak tersedia</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label text-muted mb-1 small">Sertifikat Terkait</label>
                                        <div class="border rounded-3 p-3 bg-light">
                                            @if($user->biodata && $user->biodata->sertifikat)
                                                @php
                                                    $sertifikats = json_decode($user->biodata->sertifikat, true);
                                                @endphp
                                                @if(is_array($sertifikats) && count($sertifikats) > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($sertifikats as $sertifikatPath)
                                                            <li class="d-flex align-items-center mb-2">
                                                                <a href="{{ asset('storage/' . $sertifikatPath) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                                                    <i class="bi bi-file-earmark-text me-1"></i> {{ pathinfo($sertifikatPath, PATHINFO_BASENAME) }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">Belum ada sertifikat diunggah</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Belum ada sertifikat diunggah</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endif
                        {{-- End of Account Tab --}}
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Non-aktifkan Pengguna</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menonaktifkan pengguna ini? Pengguna tidak akan bisa login setelah dinonaktifkan.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Non-aktifkan</button>
                        </div>
                    </form>
                </div>
            </div>
            @endsection