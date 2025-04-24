@extends('layouts.karyawan.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Detail Kegiatan</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $activity->nama_kegiatan }}</h5>

            <dl class="row mt-3">
                <dt class="col-sm-3">Jenjang</dt>
                <dd class="col-sm-9">{{ $activity->jenjang->nama_jenjang ?? '-' }}</dd>

                <dt class="col-sm-3">Deskripsi</dt>
                <dd class="col-sm-9">{{ $activity->deskripsi ?? '-' }}</dd>

                <dt class="col-sm-3">Waktu Mulai</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($activity->waktu_mulai)->format('d M Y H:i') }}</dd>

                <dt class="col-sm-3">Waktu Akhir</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($activity->waktu_akhir)->format('d M Y H:i') }}</dd>

                <dt class="col-sm-3">Jumlah Peserta</dt>
                <dd class="col-sm-9">{{ $activity->jumlah_peserta ?? '-' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $activity->status == 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($activity->status) }}
                    </span>
                </dd>
            </dl>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="card shadow-sm my-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Daftar Peserta</h4>
                <a href="{{ route('activities.participants.add', $activity->id) }}" class="btn btn-success">
                    + Tambah Peserta
                </a>
            </div>

            <!-- Form pencarian -->
            <form method="GET" action="{{ route('activities.show', $activity->id) }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari peserta..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($participants as $index => $user)
                        <tr>
                            <td>{{ ($participants->currentPage() - 1) * $participants->perPage() + $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->jenjang->nama_jenjang ?? '-' }}</td>
                            <td>
                                @if ($user->pivot->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('activities.participants.remove', ['activity' => $activity->id, 'user' => $user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada peserta</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $participants->links() }}
            </div>
        </div>
    </div>
    <div class="mt-4">
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>
@endsection
