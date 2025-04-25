@extends('layouts.peserta.template')
@section('content')
<h1 class="text-xl font-bold mb-4">Sertifikat Saya</h1>
<table class="table-auto w-full">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($certificates as $certificate)
        <tr>
            <td>{{ $certificate->kode_sertifikat }}</td>
            <td>{{ $certificate->course ? 'Course' : 'Activity' }}</td>
            <td>{{ $certificate->status }}</td>
            <td>
                <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">Detail</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection