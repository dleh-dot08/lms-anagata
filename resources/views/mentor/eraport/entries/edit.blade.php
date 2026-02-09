@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Input / Edit E-Raport</h4>
            <small class="text-muted">
                Entry #{{ $entry->id }} — Siswa: {{ $entry->student->name ?? ('User #'.$entry->user_id) }}
            </small>
        </div>

        <a href="{{ route('mentor.eraport.batches.show', $entry->batch_id) }}" class="btn btn-outline-secondary">
            Kembali ke Batch
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Validasi gagal:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if($entry->locked_at)
        <div class="alert alert-warning">
            Entry ini sedang terkunci (batch sudah publish). Tidak bisa diubah.
        </div>
    @endif

    <form action="{{ route('mentor.eraport.entries.update', $entry) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">Info Umum</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Platform</label>
                        <input type="text" name="platform" class="form-control"
                               value="{{ old('platform', $entry->platform) }}"
                               placeholder="mis: Scratch / Roblox / Web / Unity"
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori Kursus</label>
                        <input type="text" class="form-control" value="{{ $courseCategoryName ?? '-' }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">Penilaian</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nilai Rata-Rata Proyek Digital (0–100)</label>
                        <input type="number" step="0.01" min="0" max="100"
                               name="avg_project_score"
                               class="form-control"
                               value="{{ old('avg_project_score', $entry->avg_project_score) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Logika / CT (0–100)</label>
                        <input type="number" step="0.01" min="0" max="100"
                               name="logic_score"
                               class="form-control"
                               value="{{ old('logic_score', $entry->logic_score) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                        <small class="text-muted">Boleh kosong jika pakai predikat saja.</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Predikat Logika/CT (opsional)</label>
                        <input type="text" name="logic_predicate" class="form-control"
                               value="{{ old('logic_predicate', $entry->logic_predicate) }}" readonly
                               placeholder="mis: Sangat Baik / Baik / Cukup"
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Kreativitas (0–100)</label>
                        <input type="number" step="0.01" min="0" max="100"
                               name="creativity_score"
                               class="form-control"
                               value="{{ old('creativity_score', $entry->creativity_score) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Predikat Kreativitas (opsional)</label>
                        <input type="text" name="creativity_predicate" class="form-control"
                               value="{{ old('creativity_predicate', $entry->creativity_predicate) }}" readonly
                               placeholder="mis: Sangat Baik / Baik / Cukup"
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan Mentor</label>
                        <textarea name="mentor_note" rows="4" class="form-control"
                                  placeholder="Tulis catatan perkembangan, saran perbaikan, dan apresiasi..."
                                  {{ $entry->locked_at ? 'disabled' : '' }}>{{ old('mentor_note', $entry->mentor_note) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">Rekap Kehadiran (opsional)</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Hadir</label>
                        <input type="number" min="0" name="hadir_count" class="form-control"
                               value="{{ old('hadir_count', $entry->hadir_count) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sakit</label>
                        <input type="number" min="0" name="sakit_count" class="form-control"
                               value="{{ old('sakit_count', $entry->sakit_count) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Izin</label>
                        <input type="number" min="0" name="izin_count" class="form-control"
                               value="{{ old('izin_count', $entry->izin_count) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alpha</label>
                        <input type="number" min="0" name="alpha_count" class="form-control"
                               value="{{ old('alpha_count', $entry->alpha_count) }}" readonly
                               {{ $entry->locked_at ? 'disabled' : '' }}>
                    </div>
                </div>

                <small class="text-muted d-block mt-2">
                    Jika kamu ingin rekap otomatis dari tabel attendances/meetings, nanti kita buatkan tombol “Tarik Otomatis”.
                </small>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" {{ $entry->locked_at ? 'disabled' : '' }}>Simpan</button>
            <a href="{{ route('mentor.eraport.batches.show', $entry->batch_id) }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
