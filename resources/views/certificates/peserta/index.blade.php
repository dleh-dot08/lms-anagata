@extends('layouts.peserta.template')

@section('content')
<div class="container px-3 px-md-4">
    <h3 class="fw-bold py-3 mb-1 mt-2 h5 h-md-3">Sertifikat Saya</h3>
    <div class="card shadow-sm p-4">

        {{-- Pesan sukses --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Pesan error --}}
        @if(session('errors'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter dan pencarian --}}
        <form method="GET" action="{{ route('certificates.indexPeserta') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari nama kursus / kegiatan...">
                </div>
                <div class="col-12 col-md-4">
                    <select name="type" class="form-control">
                        <option value="">Semua Tipe</option>
                        <option value="course" {{ request()->type == 'course' ? 'selected' : '' }}>Kursus</option>
                        <option value="activity" {{ request()->type == 'activity' ? 'selected' : '' }}>Aktivitas</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        {{-- Tabel Sertifikat --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Kode</th>
                        <th>Tanggal Terbit</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($certificates as $certificate)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($certificate->course)
                                {{ $certificate->course->nama_kelas }}
                            @elseif ($certificate->activity)
                                {{ $certificate->activity->nama_kegiatan }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $certificate->course ? 'Kursus' : ($certificate->activity ? 'Aktivitas' : '-') }}
                        </td>
                        <td>{{ $certificate->kode_sertifikat }}</td>
                        <td>{{ \Carbon\Carbon::parse($certificate->tanggal_terbit)->format('d M Y') }}</td>
                        <td>
                            @if ($certificate->status == 'Diterbitkan')
                                <span class="badge bg-success">Diterbitkan</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Diterbitkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('certificates.showPeserta', $certificate->id) }}" class="btn btn-info btn-sm mb-1">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @if ($certificate->status == 'Diterbitkan')
                                <a href="{{ asset('storage/sertifikat/' . $certificate->file_sertifikat) }}" target="_blank" class="btn btn-success btn-sm mb-1">
                                    <i class="bi bi-download"></i> Lihat
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada sertifikat yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $certificates->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
</div>
@endsection
