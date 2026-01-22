@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">E-Raport — Batch (Mentor)</h4>
            <small class="text-muted">Pilih batch untuk input nilai & catatan. Batch publish akan terkunci.</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Course</th>
                        <th style="width:170px;">Semester</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                        @php
                            $map = [
                                'draft'=>'secondary',
                                'validating'=>'warning',
                                'ready'=>'info',
                                'published'=>'success',
                                'reopened'=>'danger',
                            ];
                            $clr = $map[$b->status] ?? 'secondary';
                            $isLocked = ($b->status === 'published');
                        @endphp
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>
                                <div class="fw-semibold">Course #{{ $b->course_id }}</div>
                                <small class="text-muted">{{ $b->course->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $b->semester_label }}</div>
                                <small class="text-muted">semester_id: {{ $b->semester_id ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $clr }}">{{ strtoupper($b->status) }}</span>
                                @if($isLocked)
                                    <div><small class="text-muted">Entry terkunci</small></div>
                                @endif
                            </td>
                            <td>
                                {{-- butuh route & method show batch untuk list entry --}}
                                <a href="{{ route('mentor.eraport.batches.show', $b) }}"
                                   class="btn btn-sm btn-outline-primary {{ $isLocked ? 'disabled' : '' }}">
                                    Lihat Entry
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada batch yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>

    <div class="mt-3 small text-muted">
        Catatan: tombol “Lihat Entry” memerlukan route `mentor.eraport.batches.show`.
        Kalau route itu belum ada di project kamu, bilang ya — nanti saya buatkan route + method controller-nya.
    </div>
</div>
@endsection
