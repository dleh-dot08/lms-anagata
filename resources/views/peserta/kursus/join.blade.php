@extends('layouts.peserta.template')

@section('content')
<div class="container mt-4">
    <h3 class="fw-bold mb-3">Gabung Kursus</h3>

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('courses.joinPeserta.submit') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="kode_unik" class="form-label">Masukkan Kode Kursus</label>
            <input type="text"
                   name="kode_unik"
                   id="kode_unik"
                   class="form-control @error('kode_unik') is-invalid @enderror"
                   value="{{ old('kode_unik') }}"
                   placeholder="Contoh: CLS-ABCDE"
                   required>
            @error('kode_unik')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Gabung</button>
        <a href="{{ route('courses.indexpeserta') }}" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>
@endsection
