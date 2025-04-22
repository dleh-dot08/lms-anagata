@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit Kegiatan</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada beberapa kesalahan dalam input kamu:<br><br>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('activities.update', $activity->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" class="form-control" value="{{ old('nama_kegiatan', $activity->nama_kegiatan) }}" required>
            </div>

            <div class="col-md-6">
                <label for="jenjang_id" class="form-label">Jenjang</label>
                <select name="jenjang_id" class="form-select" required>
                    <option value="">-- Pilih Jenjang --</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang->id }}" {{ $activity->jenjang_id == $jenjang->id ? 'selected' : '' }}>
                            {{ $jenjang->nama_jenjang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                <input type="datetime-local" name="waktu_mulai" class="form-control" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($activity->waktu_mulai)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="col-md-6">
                <label for="waktu_akhir" class="form-label">Waktu Akhir</label>
                <input type="datetime-local" name="waktu_akhir" class="form-control" value="{{ old('waktu_akhir', \Carbon\Carbon::parse($activity->waktu_akhir)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="col-12">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $activity->deskripsi) }}</textarea>
            </div>

            <div class="col-md-6">
                <label for="jumlah_peserta" class="form-label">Jumlah Peserta</label>
                <input type="number" name="jumlah_peserta" class="form-control" value="{{ old('jumlah_peserta', $activity->jumlah_peserta) }}">
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Aktif" {{ $activity->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ $activity->status == 'Nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Update Kegiatan
            </button>
            <a href="{{ route('activities.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle me-1"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
