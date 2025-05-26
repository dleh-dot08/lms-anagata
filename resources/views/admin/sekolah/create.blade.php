@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Sekolah /</span> Tambah Sekolah
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Form Tambah Sekolah</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sekolah.store') }}" method="POST">
        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama_sekolah">Nama Sekolah</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" 
                                       id="nama_sekolah" name="nama_sekolah" value="{{ old('nama_sekolah') }}" 
                                       placeholder="Masukkan nama sekolah" required>
                                @error('nama_sekolah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jenjang_id">Jenjang</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('jenjang_id') is-invalid @enderror" 
                                        id="jenjang_id" name="jenjang_id" required>
                                    <option value="">Pilih Jenjang</option>
                                    @foreach($jenjang as $j)
                                        <option value="{{ $j->id }}" {{ old('jenjang_id') == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama_jenjang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenjang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="alamat">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                          id="alamat" name="alamat" rows="3" 
                                          placeholder="Masukkan alamat sekolah" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pic_id">PIC Sekolah</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('pic_id') is-invalid @enderror" 
                                        id="pic_id" name="pic_id">
                                    <option value="">Pilih PIC Sekolah</option>
                                    @foreach($available_pics as $pic)
                                        <option value="{{ $pic->id }}" {{ old('pic_id') == $pic->id ? 'selected' : '' }}>
                                            {{ $pic->name }} ({{ $pic->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih user dengan role Sekolah yang belum ditugaskan ke sekolah lain</small>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('admin.sekolah.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
        </div>
                    </form>
        </div>
        </div>
        </div>
    </div>
</div>
@endsection
