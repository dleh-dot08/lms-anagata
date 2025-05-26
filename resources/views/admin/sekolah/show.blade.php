@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Sekolah /</span> Detail Sekolah
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Sekolah</h5>
                    <a href="{{ route('admin.sekolah.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">Nama Sekolah</label>
                        <div class="col-sm-10">
                            {{ $sekolah->nama_sekolah }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">Jenjang</label>
                        <div class="col-sm-10">
                            {{ $sekolah->jenjang->nama_jenjang }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">Alamat</label>
                        <div class="col-sm-10">
                            {{ $sekolah->alamat }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">PIC Sekolah</label>
                        <div class="col-sm-10">
                            @if($sekolah->pic)
                                <span class="badge bg-label-info">{{ $sekolah->pic->name }} ({{ $sekolah->pic->email }})</span>
                            @else
                                <span class="badge bg-label-warning">Belum ada PIC</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">Tanggal Dibuat</label>
                        <div class="col-sm-10">
                            {{ $sekolah->created_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 fw-bold">Terakhir Diupdate</label>
                        <div class="col-sm-10">
                            {{ $sekolah->updated_at->format('d M Y H:i') }}
                        </div>
                    </div>

                    <!-- Daftar Peserta -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Daftar Peserta</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Terdaftar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sekolah->peserta as $peserta)
                                            <tr>
                                                <td>{{ $peserta->name }}</td>
                                                <td>{{ $peserta->email }}</td>
                                                <td>{{ $peserta->created_at->format('d M Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada peserta terdaftar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 