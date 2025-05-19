@extends('layouts.peserta.template')

@section('content')
<div class="container mt-4">
    <h4>Pengumpulan Tugas</h4>
    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $assignment->judul }}</h5>
            <p>{!! nl2br(e($assignment->deskripsi)) !!}</p>
            @if($assignment->file_attachment)
                <p>📎 <a href="{{ Storage::url($assignment->file_attachment) }}" target="_blank">Download Lampiran</a></p>
            @endif
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y H:i') }}</p>
        </div>
    </div>

    @if($submission)
        <div class="alert alert-success">
            ✅ Kamu sudah mengumpulkan tugas ini.
        </div>
        <div class="card">
            <div class="card-body">
                <p><strong>File Tugas:</strong><br>
                    <a href="{{ Storage::url($submission->file_submission) }}" target="_blank">
                        📎 Lihat File
                    </a>
                </p>
                <p><strong>Catatan:</strong><br> {{ $submission->catatan ?? '-' }}</p>
                <p><strong>Tanggal Pengumpulan:</strong> {{ $submission->created_at->translatedFormat('d M Y H:i') }}</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">Form Pengumpulan</div>
            <form method="POST" action="{{ route('assignments.submit', $assignment->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="file_submission" class="form-label">Upload File</label>
                        <input type="file" name="file_submission" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Kumpulkan Tugas</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection
