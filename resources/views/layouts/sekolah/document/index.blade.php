@extends('layouts.sekolah.template') {{-- Sesuaikan dengan layout admin sekolah Anda --}}

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Manajemen Dokumen Sekolah: {{ $sekolah->nama_sekolah }}</h2>

    {{-- Pesan Sukses/Error (tetap sama) --}}
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
    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Perhatian!</strong> Ada beberapa masalah dengan input Anda.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Unggah Dokumen Sekolah (sedikit perubahan di dropdown) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">Unggah Dokumen Sekolah & Daftar Peserta</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sekolah.documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="document_type" class="form-label">Jenis Dokumen:</label>
                    <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" name="document_type" required>
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        @foreach($documentTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('document_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="document_file" class="form-label">Pilih File (PDF/JPG/PNG/XLSX/XLS - Max 5MB):</label>
                    <input type="file" class="form-control @error('document_file') is-invalid @enderror" id="document_file" name="document_file" required>
                    @error('document_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Untuk dokumen seperti KTP/Sertifikat/NPWP/Logo, file yang sudah ada akan diganti jika Anda mengunggah jenis dokumen yang sama.
                        Untuk Daftar Peserta (Excel), Anda bisa mengunggah berkali-kali.
                    </small>
                </div>
                <button type="submit" class="btn btn-primary">Unggah Dokumen</button>
            </form>

            <hr class="my-4"> {{-- Pemisah visual --}}

            {{-- Bagian Download Template Excel Peserta --}}
            <h5 class="mb-3">Unduh Template Daftar Peserta Kursus</h5>
            <p>Untuk mengunggah daftar peserta, silakan unduh *template* Excel berikut. Isi data peserta sesuai dengan kolom yang tersedia, lalu unggah menggunakan form di atas dengan memilih jenis dokumen "Daftar Peserta Kursus (Excel)".</p>
            <a href="{{ route('sekolah.documents.download_participant_template') }}" class="btn btn-success">
                <i class="bi bi-download me-2"></i> Unduh Template Excel Peserta
            </a>
            <p class="small text-muted mt-3">Jangan mengubah nama kolom pada *header* *template*. Pastikan format tanggal lahir **YYYY-MM-DD** dan Jenis Kelamin **Pria/Wanita**.</p>
        </div>
    </div>

    {{-- Daftar Dokumen yang Sudah Diunggah (tetap sama) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Dokumen Sekolah & Peserta yang Sudah Diunggah</h5>
        </div>
        <div class="card-body">
            @if($documents->isEmpty() && count($documentTypes) > 0)
                <p class="text-center text-muted">Belum ada dokumen sekolah atau daftar peserta yang diunggah.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Jenis Dokumen</th>
                                <th>Nama File</th>
                                <th class="text-center">Ukuran</th>
                                <th class="text-center">Tanggal Unggah</th>
                                <th>Diunggah Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentTypes as $key => $label)
                                @php
                                    // Untuk 'daftar_peserta', kita mungkin punya banyak file, jadi ambil semua yang relevan
                                    $docsOfType = ($key === 'daftar_peserta')
                                                  ? $documents->where('document_type', $key)
                                                  : collect([$documents->firstWhere('document_type', $key)]);
                                @endphp

                                @forelse($docsOfType as $doc)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>
                                            <a href="{{ route('sekolah.documents.download', ['document' => $doc->id]) }}" target="_blank" class="text-decoration-none text-primary">
                                                <i class="bi bi-file-earmark{{ Str::endsWith($doc->mime_type, 'pdf') ? '-pdf-fill' : (Str::startsWith($doc->mime_type, 'image/') ? '-image-fill' : (Str::endsWith($doc->mime_type, 'spreadsheetml.sheet') || Str::endsWith($doc->mime_type, 'excel') ? '-excel-fill' : '-fill')) }} me-1"></i>
                                                {{ $doc->file_name }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($doc->file_size / 1024, 2) }} KB
                                        </td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            @if($doc->uploadedBy)
                                                {{ $doc->uploadedBy->name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('sekolah.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Dokumen">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    @if ($key !== 'daftar_peserta') {{-- Hanya tampilkan "Belum diunggah" untuk dokumen non-daftar_peserta --}}
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td colspan="5" class="text-center text-muted fst-italic">Belum diunggah</td>
                                        </tr>
                                    @endif
                                @endforelse
                            @endforeach
                            {{-- Jika daftar_peserta sudah ada yang diunggah, dia akan muncul di loop forelse di atas --}}
                            @if($documents->where('document_type', 'daftar_peserta')->isEmpty())
                                <tr>
                                    <td>Daftar Peserta Kursus (Excel)</td>
                                    <td colspan="5" class="text-center text-muted fst-italic">Belum ada daftar peserta yang diunggah.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection