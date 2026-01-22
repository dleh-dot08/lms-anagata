@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Edit Template E-Raport</h4>
            <small class="text-muted">ID: {{ $template->id }}</small>
        </div>
        <a href="{{ route('admin.eraport.templates.index') }}" class="btn btn-outline-secondary">Kembali</a>
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

    <form action="{{ route('admin.eraport.templates.update', $template) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Code (opsional)</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $template->code) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Jenjang ID (opsional)</label>
                        <input type="number" name="jenjang_id" class="form-control" value="{{ old('jenjang_id', $template->jenjang_id) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipe Layout <span class="text-danger">*</span></label>
                        <select name="layout_type" id="layout_type" class="form-select" required>
                            <option value="html" {{ old('layout_type', $template->layout_type)==='html' ? 'selected' : '' }}>HTML</option>
                            <option value="background_overlay" {{ old('layout_type', $template->layout_type)==='background_overlay' ? 'selected' : '' }}>Background Overlay</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
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
                        <input type="text" name="view_path" class="form-control"
                               value="{{ old('view_path', $template->view_path) }}"
                               placeholder="mis: eraport.templates.sma_v1">
                    </div>

                    <div class="col-12">
                        <label class="form-label">HTML Raw (opsional)</label>
                        <textarea name="html" class="form-control" rows="6">{{ old('html', $template->html) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">CSS Raw (opsional)</label>
                        <textarea name="css" class="form-control" rows="5">{{ old('css', $template->css) }}</textarea>
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
                        <label class="form-label">Upload Background Baru (opsional)</label>
                        <input type="file" name="background_file" class="form-control">
                        @if($template->background_path)
                            <small class="text-muted d-block mt-2">
                                Current: <a href="{{ asset('storage/'.$template->background_path) }}" target="_blank">Lihat background</a>
                            </small>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label">Field Map (JSON)</label>
                        <textarea name="field_map" class="form-control" rows="7">{{ old('field_map', is_array($template->field_map) ? json_encode($template->field_map, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : $template->field_map) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONFIG --}}
        <div class="card mb-3">
            <div class="card-header bg-light fw-semibold">Config (opsional)</div>
            <div class="card-body">
                <label class="form-label">Config JSON</label>
                <textarea name="config" class="form-control" rows="5">{{ old('config', is_array($template->config) ? json_encode($template->config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : $template->config) }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Update</button>
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
