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
                        <th>Status</th> {{-- Kolom baru --}}
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                    @php
                        $submission = $submissions[$assignment->id] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $assignment->judul }}</td>
                        <td>
                            <div><strong>Kursus:</strong> {{ optional($assignment->meeting->course)->nama_kelas ?? '-' }}</div>
                            <div><strong>Pertemuan:</strong> {{ $assignment->meeting->judul ?? '-' }}</div>
                        </td>
                        <td style="max-width: 300px;">
                            {{ Str::limit($assignment->deskripsi, 100, '...') }}
                        </td>
                        <td>
                            {{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') : '-' }}
                        </td>
                        <td>
                            @if ($submission)
                                <div class="text-success fw-semibold">Sudah dikumpulkan</div>
                                <div class="small text-muted">Pada: {{ \Carbon\Carbon::parse($submission->created_at)->format('d M Y H:i') }}</div>
                                <div class="small">
                                    <a href="{{ asset('storage/'.$submission->file_submission) }}" target="_blank">Lihat File</a>
                                </div>
                            @else
                                <span class="text-muted">Belum dikumpulkan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!$assignment->deadline || \Carbon\Carbon::now()->lte($assignment->deadline))
                            <button class="btn btn-sm {{ $submission ? 'btn-warning' : 'btn-primary' }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAssignment{{ $assignment->id }}">
                                {{ $submission ? 'Edit Tugas' : 'Kumpulkan Tugas' }}
                            </button>
                            @else
                                <span class="badge bg-secondary">Deadline Lewat</span>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="modalAssignment{{ $assignment->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $assignment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ $submission ? route('assignments.update', $assignment->id) : route('assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel{{ $assignment->id }}">
                                            {{ $submission ? 'Edit Pengumpulan Tugas' : 'Kumpulkan Tugas' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="file_submission" class="form-label">File Tugas</label>
                                            <input type="file" name="file_submission" class="form-control" {{ $submission ? '' : 'required' }}>
                                            @if ($submission && $submission->file_submission)
                                                <small class="text-muted d-block mt-1">File sebelumnya:
                                                    <a href="{{ asset('storage/'.$submission->file_submission) }}" target="_blank">Lihat</a>
                                                </small>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="catatan" class="form-label">Catatan (opsional)</label>
                                            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $submission->catatan ?? '') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">{{ $submission ? 'Update' : 'Submit' }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Tidak ada tugas yang tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
