@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">📄 Pengumpulan Tugas: <strong>{{ $assignment->judul }}</strong></h4>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                    ← Kembali
                </a>
            </div>

            @if($assignment->submissions->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>👤 Nama Siswa</th>
                                <th>📝 Catatan</th>
                                <th>📎 File</th>
                                <th>🕒 Waktu Submit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignment->submissions as $submission)
                                <tr>
                                    <td class="text-mid">
                                        <strong>{{ $submission->user->name }}</strong>
                                    </td>
                                    <td class="text-start">
                                        {{ $submission->catatan ?? '-' }}
                                    </td>
                                    <td>
                                        @if($submission->file_submission)
                                            <a href="{{ Storage::url($submission->file_submission) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                📥 Download
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $submission->created_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    Belum ada siswa yang mengumpulkan tugas ini.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
