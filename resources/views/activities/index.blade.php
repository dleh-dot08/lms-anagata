@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Daftar Kegiatan</h3>
        <a href="{{ route('activities.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah Kegiatan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if($activities->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Jenjang</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Akhir</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $index => $activity)
                        <tr>
                            <td>{{ $loop->iteration + ($activities->currentPage() - 1) * $activities->perPage() }}</td>
                            <td>{{ $activity->nama_kegiatan }}</td>
                            <td>{{ $activity->jenjang->nama_jenjang ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->waktu_mulai)->format('d M Y H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($activity->waktu_akhir)->format('d M Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $activity->status == 'Aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('activities.show', $activity->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                                <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $activities->onEachSide(1)->links('pagination.custom') }}
        </div>
        
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Belum ada kegiatan yang ditambahkan.
        </div>
    @endif
</div>
@endsection
