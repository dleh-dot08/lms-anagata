@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Tambah Kelas Baru</h2>

                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Kelas</label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_jenjang" class="form-label">Jenjang</label>
                        <select name="id_jenjang" id="id_jenjang" class="form-control @error('id_jenjang') is-invalid @enderror" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach($jenjangs as $jenjang)
                                <option value="{{ $jenjang->id }}" {{ old('id_jenjang') == $jenjang->id ? 'selected' : '' }}>
                                    {{ $jenjang->nama_jenjang }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 