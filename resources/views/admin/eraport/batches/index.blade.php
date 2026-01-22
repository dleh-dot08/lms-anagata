@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">E-Raport — Batch</h4>
            <small class="text-muted">Batch penerbitan rapor per course/semester.</small>
        </div>
        <a href="{{ route('admin.eraport.batches.create') }}" class="btn btn-primary">+ Buat Batch</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="draft" {{ request('status')==='draft'?'selected':'' }}>draft</option>
                        <option value="validating" {{ request('status')==='validating'?'selected':'' }}>validating</option>
                        <option value="ready" {{ request('status')==='ready'?'selected':'' }}>ready</option>
                        <option value="published" {{ request('status')==='published'?'selected':'' }}>published</option>
                        <option value="reopened" {{ request('status')==='reopened'?'selected':'' }}>reopened</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course ID</label>
                    <input type="number" name="course_id" class="form-control" value="{{ request('course_id') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.eraport.batches.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Course</th>
                        <th style="width:180px;">Semester</th>
                        <th style="width:180px;">Template</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>
                                <div class="fw-semibold">Course #{{ $b->course_id }}</div>
                                <small class="text-muted">{{ $b->course->nama_kelas ?? '-' }}</small>
                            </td>
                            <td>
                                <div>{{ $b->semester_label }}</div>
                                <small class="text-muted">semester_id: {{ $b->semester_id ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $b->template->name ?? '-' }}</div>
                                <small class="text-muted">{{ $b->template->layout_type ?? '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $map = [
                                        'draft'=>'secondary',
                                        'validating'=>'warning',
                                        'ready'=>'info',
                                        'published'=>'success',
                                        'reopened'=>'danger',
                                    ];
                                    $clr = $map[$b->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $clr }}">{{ strtoupper($b->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.eraport.batches.show', $b) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada batch.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
