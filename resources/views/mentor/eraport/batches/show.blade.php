@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Batch #{{ $batch->id }} — Entry Siswa</h4>
            <small class="text-muted">
                Course #{{ $batch->course_id }} — {{ $batch->course->nama_kelas ?? '-' }} |
                Semester: {{ $batch->semester_label }}
            </small>
        </div>
        <a href="{{ route('mentor.eraport.batches.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $locked = ($batch->status === 'published');
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge bg-{{ $locked ? 'danger' : 'success' }}">
                    {{ $locked ? 'LOCKED (PUBLISHED)' : 'OPEN' }}
                </span>
                <span class="text-muted small">
                    {{ $locked ? 'Batch sudah publish, tidak bisa edit.' : 'Silakan input nilai/catatan per siswa.' }}
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light fw-semibold">
            Daftar Entry ({{ $entries->total() }})
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Siswa</th>
                        <th style="width:120px;">Avg Proyek</th>
                        <th style="width:140px;">Logika/CT</th>
                        <th style="width:140px;">Kreativitas</th>
                        <th>Catatan</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                        <tr>
                            <td>{{ $e->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $e->student->name ?? ('User #'.$e->user_id) }}</div>
                                <small class="text-muted">user_id: {{ $e->user_id }}</small>
                            </td>
                            <td>{{ $e->avg_project_score ?? '-' }}</td>
                            <td>{{ $e->logic_score ?? ($e->logic_predicate ?? '-') }}</td>
                            <td>{{ $e->creativity_score ?? ($e->creativity_predicate ?? '-') }}</td>
                            <td style="min-width:260px;">
                                <div class="text-truncate" style="max-width:520px;">
                                    {{ $e->mentor_note ? \Illuminate\Support\Str::limit($e->mentor_note, 120) : '-' }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('mentor.eraport.entries.edit', $e) }}"
                                   class="btn btn-sm btn-outline-primary {{ $locked || $e->locked_at ? 'disabled' : '' }}">
                                    {{ ($locked || $e->locked_at) ? 'Terkunci' : 'Input/Edit' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada entry.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
