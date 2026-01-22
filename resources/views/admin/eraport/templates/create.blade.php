@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Tambah Template E-Raport</h4>
            <small class="text-muted">Buat template baru (HTML / Background Overlay).</small>
        </div>
        <a href="{{ route('admin.eraport.templates.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Validasi gagal:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.eraport.templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Code (opsional)</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="mis: SMA_V1">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jenjang ID (opsional)</label>
                        <input type="number" name="jenjang_id" class="form-control" value="{{ old('jenjang_id') }}" placeholder="mis: 3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipe Layout <span class="text-danger">*</span></label>
                        <select name="layout_type" id="layout_type" class="form-select" required>
                            <option value="html" {{ old('layout_type','html')==='html' ? 'selected' : '' }}>HTML (Blade View / HTML raw)</option>
                            <option value="background_overlay" {{ old('layout_type')==='background_overlay' ? 'selected' : '' }}>Background Overlay (gambar/pdf + posisi field)</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active',1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- HTML MODE --}}
        <div class="card mb-3" id="box_html">
            <div class="card-header bg-light fw-semibold">Mode HTML</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Blade View Path (opsional)</label>
                        <input type="text" name="view_path" class="form-control" value="{{ old('view_path') }}"
                               placeholder="mis: eraport.templates.sma_v1">
                        <small class="text-muted">Jika diisi & view ada, sistem akan render blade view ini.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label">HTML Raw (opsional)</label>
                        <textarea name="html" class="form-control" rows="6" placeholder="Jika tidak pakai blade, bisa simpan HTML di sini.">{{ old('html') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">CSS Raw (opsional)</label>
                        <textarea name="css" class="form-control" rows="5" placeholder="CSS tambahan">{{ old('css') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- OVERLAY MODE --}}
        <div class="card mb-3 d-none" id="box_overlay">
            <div class="card-header bg-light fw-semibold">Mode Background Overlay</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Upload Background (PNG/JPG/PDF)</label>
                        <input type="file" name="background_file" class="form-control">
                        <small class="text-muted">Untuk overlay: gambar/pdf jadi background, field ditempel di posisi tertentu.</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Field Map (JSON) — posisi & mapping</label>
                        <textarea name="field_map" class="form-control" rows="7" placeholder='{"nama":{"x":100,"y":120},"kelas":{"x":100,"y":140}}'>{{ old('field_map') }}</textarea>
                        <small class="text-muted">Nanti dipakai kalau Anda render overlay secara programatik.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONFIG --}}
        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">Config (opsional)</div>
            <div class="card-body">
                <label class="form-label">Config JSON</label>
                <textarea name="config" class="form-control" rows="5" placeholder='{"paper":"A4","orientation":"portrait"}'>{{ old('config') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.eraport.templates.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
(function(){
    const layout = document.getElementById('layout_type');
    const boxHtml = document.getElementById('box_html');
    const boxOverlay = document.getElementById('box_overlay');

    function sync(){
        const v = layout.value;
        if(v === 'html'){
            boxHtml.classList.remove('d-none');
            boxOverlay.classList.add('d-none');
        }else{
            boxOverlay.classList.remove('d-none');
            boxHtml.classList.add('d-none');
        }
    }
    layout.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
