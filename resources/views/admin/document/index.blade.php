@extends('layouts.admin.template') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Dokumen Unggahan Sekolah</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Section (Opsional) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0 text-white">Filter Dokumen</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.school_documents.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="sekolah_id" class="form-label">Filter Sekolah:</label>
                        <select class="form-select" id="sekolah_id" name="sekolah_id">
                            <option value="">Semua Sekolah</option>
                            @foreach($listSekolah as $sekolahItem)
                                <option value="{{ $sekolahItem->id }}" {{ request('sekolah_id') == $sekolahItem->id ? 'selected' : '' }}>
                                    {{ $sekolahItem->nama_sekolah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="document_type" class="form-label">Filter Jenis Dokumen:</label>
                        <select class="form-select" id="document_type" name="document_type">
                            <option value="">Semua Jenis</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('document_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Terapkan Filter</button>
                        <a href="{{ route('admin.school_documents.index') }}" class="btn btn-outline-secondary">Reset Filter</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Dokumen --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">Daftar Dokumen</h5>
        </div>
        <div class="card-body">
            @if($documents->isEmpty())
                <p class="text-center text-muted">Tidak ada dokumen yang ditemukan.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sekolah</th>
                                <th>Jenis Dokumen</th>
                                <th>Nama File</th>
                                <th class="text-center">Ukuran</th>
                                <th class="text-center">Tanggal Unggah</th>
                                <th>Diunggah Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                                <tr>
                                    <td>{{ $doc->sekolah->nama_sekolah ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            // Mencocokkan key dengan label yang readable
                                            echo $documentTypes[$doc->document_type] ?? $doc->document_type;
                                        @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.school_documents.download', $doc) }}" class="text-decoration-none text-primary" target="_blank">
                                            <i class="bi bi-file-earmark{{ Str::endsWith($doc->mime_type, 'pdf') ? '-pdf-fill' : (Str::startsWith($doc->mime_type, 'image/') ? '-image-fill' : '-fill') }} me-1"></i>
                                            {{ $doc->file_name }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y H:i') }}</td>
                                    <td>{{ $doc->uploadedBy->name ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.school_documents.download', $doc) }}" class="btn btn-sm btn-info" title="Unduh Dokumen">
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                        {{-- Jika admin bisa menghapus --}}
                                        {{--
                                        <form action="{{ route('admin.school_documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Dokumen">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                        --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection