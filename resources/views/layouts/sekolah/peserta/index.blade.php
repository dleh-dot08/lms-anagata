@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Peserta /</span> Daftar Peserta
    </h4>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sekolah.peserta') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari nama, email, atau nomor HP..." 
                               name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="kelas_id" onchange="this.form.submit()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Participant List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th>Kelas</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($peserta as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($p->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $p->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $p->email }}</td>
                                <td>{{ $p->biodata->no_hp ?? '-' }}</td>
                                <td>{{ $p->biodata->alamat ?? '-' }}</td>
                                <td>{{ $p->kelas->nama_kelas ?? '-' }}</td>
                                <td>{{ $p->created_at->format('d M Y') }}</td>
                                <td>
                                    <!-- Direct Button -->
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $p->id }}">
                                        <i class="bx bx-show-alt me-1"></i> Detail
                                    </button>

                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Peserta</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Nama</dt>
                                                                <dd class="col-sm-8">{{ $p->name }}</dd>

                                                                <dt class="col-sm-4">Email</dt>
                                                                <dd class="col-sm-8">{{ $p->email }}</dd>

                                                                <dt class="col-sm-4">No. HP</dt>
                                                                <dd class="col-sm-8">{{ $p->biodata->no_hp ?? '-' }}</dd>

                                                                <dt class="col-sm-4">Alamat</dt>
                                                                <dd class="col-sm-8">{{ $p->biodata->alamat ?? '-' }}</dd>

                                                                <dt class="col-sm-4">Kelas</dt>
                                                                <dd class="col-sm-8">{{ $p->kelas->nama_kelas ?? '-' }}</dd>
                                                            </dl>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Terdaftar</dt>
                                                                <dd class="col-sm-8">{{ $p->created_at->format('d M Y H:i') }}</dd>

                                                                <dt class="col-sm-4">Email Terverifikasi</dt>
                                                                <dd class="col-sm-8">
                                                                    @if($p->email_verified_at)
                                                                        <span class="badge bg-label-success ms-3">Ya</span>
                                                                    @else
                                                                        <span class="badge bg-label-danger ms-3">Belum</span>
                                                                    @endif
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data peserta</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $peserta->onEachSide(1)->links('pagination.custom') }}
            </div>
        </div>
    </div>
</div>
@endsection