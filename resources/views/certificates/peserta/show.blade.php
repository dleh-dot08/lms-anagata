@extends('layouts.peserta.template')
@section('content')
<h1 class="text-xl font-bold mb-4">Detail Sertifikat</h1>
<ul class="list-disc ml-5">
    <li>Kode: {{ $certificate->kode_sertifikat }}</li>
    <li>Tipe: {{ $certificate->course ? 'Course' : 'Activity' }}</li>
    <li>Status: {{ $certificate->status }}</li>
    <li>Tanggal Terbit: {{ $certificate->tanggal_terbit }}</li>
    <li>File: <a href="{{ asset('storage/' . $certificate->file_sertifikat) }}" target="_blank">Download</a></li>
</ul>
@endsection