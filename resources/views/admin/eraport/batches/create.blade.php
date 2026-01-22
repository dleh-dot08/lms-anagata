@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Buat Batch E-Raport</h4>
            <small class="text-muted">Batch akan membuat daftar entry siswa dari enrollment course.</small>
        </div>
        <a href="{{ route('admin.eraport.batches.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Validasi gagal:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.eraport.batches.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
                            <option value="">-- pilih course --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ (string)old('course_id')===(string)$c->id?'selected':'' }}>
                                    #{{ $c->id }} — {{ $c->nama_kelas ?? 'Course' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pastikan peserta sudah ter-enroll ke course.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Template <span class="text-danger">*</span></label>
                        <select name="template_id" class="form-select" required>
                            <option value="">-- pilih template --</option>
                            @foreach($templates as $t)
                                <option value="{{ $t->id }}" {{ (string)old('template_id')===(string)$t->id?'selected':'' }}>
                                    #{{ $t->id }} — {{ $t->name }} ({{ $t->layout_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select" required>
                            <option value="">-- Pilih Semester --</option>
                            @foreach($semesters as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->name }} {{ $s->year }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Buat Batch (DRAFT)</button>
                    <a href="{{ route('admin.eraport.batches.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
