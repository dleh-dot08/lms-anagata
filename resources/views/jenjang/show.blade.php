@extends('layouts.admin.template')

@section('content')
    <h2>Detail Jenjang</h2>

    <div class="mb-3">
        <strong>Nama Jenjang:</strong> {{ $jenjang->nama_jenjang }}
    </div>

    <div class="mb-3">
        <strong>Deskripsi:</strong> {{ $jenjang->description ?? 'Tidak ada deskripsi' }}
    </div>

    <div class="mb-3">
        <strong>Deleted At:</strong> {{ $jenjang->deleted_at ?? 'Aktif' }}
    </div>

    <a href="{{ route('jenjang.index') }}" class="btn btn-secondary">Kembali</a>
@endsection
