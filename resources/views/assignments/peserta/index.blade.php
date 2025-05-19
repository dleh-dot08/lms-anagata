@extends('layouts.peserta.template')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold text-primary">Daftar Tugas</h4>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul Tugas</th>
                        <th>Kursus / Pertemuan</th>
                        <th>Deskripsi</th>
                        <th>Deadline</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                    <tr>
                        <td class="fw-semibold text-truncate" style="max-width: 180px;">
                            {{ $assignment->judul }}
                        </td>
                        <td>
                            <div><strong>Kursus:</strong> {{ optional($assignment->meeting->course)->nama_kelas ?? 'Tidak ada kursus' }}</div>
                            <div><strong>Pertemuan:</strong> {{ $assignment->meeting->judul ?? '-' }}</div>
                        </td>
                        <td style="max-width: 300px; white-space: normal;">
                            {{ Str::limit($assignment->deskripsi, 100, '...') }}
                        </td>
                        <td>
                            {{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="text-center">
                            <!-- Trigger modal -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalSubmitAssignment{{ $assignment->id }}">
                                Kumpulkan Tugas
                            </button>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="modalSubmitAssignment{{ $assignment->id }}" tabindex="-1" aria-labelledby="submitAssignmentLabel{{ $assignment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form 
                                    action="{{ route('assignments.submit', $assignment->id) }}" 
                                    method="POST" 
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="submitAssignmentLabel{{ $assignment->id }}">Kumpulkan Tugas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>{{ $assignment->judul }}</strong></p>

                                        <div class="mb-3">
                                            <label for="file_submission{{ $assignment->id }}" class="form-label">Upload File Tugas</label>
                                            <input 
                                                type="file" 
                                                class="form-control @error('file_submission') is-invalid @enderror" 
                                                id="file_submission{{ $assignment->id }}" 
                                                name="file_submission" 
                                                required>
                                            @error('file_submission')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="catatan{{ $assignment->id }}" class="form-label">Catatan (Opsional)</label>
                                            <textarea 
                                                class="form-control @error('catatan') is-invalid @enderror" 
                                                id="catatan{{ $assignment->id }}" 
                                                name="catatan"
                                                rows="3">{{ old('catatan') }}</textarea>
                                            @error('catatan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Kumpulkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada tugas yang tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
