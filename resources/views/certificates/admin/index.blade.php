@extends('layouts.admin.template')
@section('content')
<h1 class="text-xl font-bold mb-4">Manajemen Sertifikat</h1>
<a href="{{ route('certificates.create') }}" class="btn btn-primary mb-3">+ Tambah Sertifikat</a>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kode</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($certificates as $certificate)
        <tr>
            <td>{{ $certificate->user->name }}</td>
            <td>{{ $certificate->kode_sertifikat }}</td>
            <td>{{ $certificate->course ? 'Course' : 'Activity' }}</td>
            <td>{{ $certificate->status }}</td>
            <td>
                <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">Detail</a>
                <a href="{{ route('certificates.edit', $certificate->id) }}" class="btn btn-sm btn-warning">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection