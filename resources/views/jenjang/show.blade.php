@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Detail Jenjang</h2>

        <div class="card mt-3">
            <div class="card-body">
                <h4>{{ $jenjang->nama_jenjang }}</h4>
                <p>{{ $jenjang->description ?? 'Tidak ada deskripsi' }}</p>
                <p><strong>Deleted At:</strong> {{ $jenjang->deleted_at ?? 'Aktif' }}</p>
            </div>
        </div>

        <a href="{{ route('jenjang.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
@endsection
