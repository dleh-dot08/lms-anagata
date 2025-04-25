@extends('layouts.admin.template')
@section('content')
<h1 class="text-xl font-bold mb-4">Edit Sertifikat</h1>
<form action="{{ route('certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="block">Kode Sertifikat</label>
        <input type="text" name="kode_sertifikat" value="{{ $certificate->kode_sertifikat }}" class="form-input">
    </div>
    <div class="mb-3">
        <label class="block">Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit" value="{{ $certificate->tanggal_terbit }}" class="form-input">
    </div>
    <div class="mb-3">
        <label class="block">Status</label>
        <select name="status" class="form-select">
            <option value="Diterbitkan" {{ $certificate->status == 'Diterbitkan' ? 'selected' : '' }}>Diterbitkan</option>
            <option value="Belum Diterbitkan" {{ $certificate->status == 'Belum Diterbitkan' ? 'selected' : '' }}>Belum Diterbitkan</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection