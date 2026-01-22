@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Detail Batch E-Raport</h4>
            <small class="text-muted">Batch #{{ $batch->id }} — Status: {{ strtoupper($batch->status) }}</small>
        </div>
        <a href="{{ route('admin.eraport.batches.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4"><b>Course:</b> #{{ $batch->course_id }} — {{ $batch->course->nama_kelas ?? '-' }}</div>
                <div class="col-md-4"><b>Semester:</b> {{ $batch->semester_label }} (semester_id: {{ $batch->semester_id ?? '-' }})</div>
                <div class="col-md-4"><b>Template:</b> {{ $batch->template->name ?? '-' }} ({{ $batch->template->layout_type ?? '-' }})</div>
            </div>

            <hr>

            <div class="d-flex flex-wrap gap-2">
                {{-- VALIDATE (JSON) --}}
                <form method="POST" action="{{ route('admin.eraport.batches.validate', $batch) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary"
                        onclick="return confirm('Jalankan validasi? Sistem akan sinkronisasi entries dari enrollments/scores/attendances.')">
                        Validasi Kelengkapan
                    </button>
                </form>


                {{-- PUBLISH --}}
                <form method="POST" action="{{ route('admin.eraport.batches.publish', $batch) }}">
                    @csrf
                    <button class="btn btn-success"
                        {{ !in_array($batch->status, ['READY','REOPENED']) ? 'disabled' : '' }}
                        onclick="return confirm('Publish batch ini? Setelah publish, entry akan terkunci.')">
                        Publish
                    </button>
                </form>

                {{-- REOPEN --}}
                <button type="button" class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#boxReopen"
                    {{ $batch->status !== 'PUBLISHED' ? 'disabled' : '' }}>
                    Reopen / Revisi
                </button>

                {{-- EXPORT ZIP --}}
                <a href="{{ route('admin.eraport.batches.exportZip', $batch) }}"
                   class="btn btn-outline-secondary {{ $batch->status !== 'PUBLISHED' ? 'disabled' : '' }}"
                   onclick="return {{ $batch->status === 'PUBLISHED' ? 'true' : 'false' }};">
                    Export ZIP PDF
                </a>
            </div>

            <div class="collapse mt-3" id="boxReopen">
                <div class="card card-body border">
                    <form method="POST" action="{{ route('admin.eraport.batches.reopen', $batch) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Alasan Reopen <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="3" required placeholder="mis: ada nilai yang salah input / catatan mentor perlu revisi"></textarea>
                        </div>
                        <button class="btn btn-warning" onclick="return confirm('Reopen batch ini? Entry akan dibuka untuk revisi.')">
                            Konfirmasi Reopen
                        </button>
                    </form>
                </div>
            </div>

            {{-- RESULT VALIDATE --}}
            @php
            $vr = session('validate_result');
            @endphp

            @if($vr)
            <div class="mt-3" id="validateBox">
                <div class="alert alert-info mb-2">
                    <div class="fw-semibold">Hasil Validasi</div>
                    <div>Status Batch: <b>{{ $vr['batch_status'] ?? '-' }}</b></div>
                    <div>Jumlah Siswa yang belum lengkap: <b>{{ $vr['missing_count'] ?? 0 }}</b></div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="fw-semibold mb-2">Detail Kekurangan</div>

                        @if(empty($vr['missing']))
                            <div class="text-success">✅ Semua data lengkap. Batch siap dipublish.</div>
                        @else
                            <ul class="mb-0">
                                @foreach($vr['missing'] as $uid => $issues)
                                    <li><b>User {{ $uid }}</b>: {{ implode(', ', $issues) }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light fw-semibold">
            Daftar Entry Siswa ({{ $entries->total() }})
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light">
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th>Siswa</th>
                        <th style="width:140px;">Avg Proyek</th>
                        <th style="width:160px;">Logika/CT</th>
                        <th style="width:160px;">Kreativitas</th>
                        <th>Catatan Mentor</th>
                        <th style="width:120px;">Locked</th>
                        <th style="width:100px;">Aksi</th>
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
                            <td style="min-width:240px;">
                                <div class="text-truncate" style="max-width:420px;">
                                    {{ $e->mentor_note ? \Illuminate\Support\Str::limit($e->mentor_note, 120) : '-' }}
                                </div>
                            </td>
                            <td>
                                @if($e->locked_at)
                                    <span class="badge bg-danger">LOCKED</span>
                                @else
                                    <span class="badge bg-success">OPEN</span>
                                @endif
                            </td>    
                            <td>
                                @php
                                    $status = $e->locked_at ? 'LOCKED' : 'OPEN';
                                    $rapor  = $eraportMap->get($e->user_id);
                                @endphp

                                {{-- Tombol PDF: enable setelah batch PUBLISHED (meski pdf_path masih null, nanti digenerate on-demand) --}}
                                @if($batch->status === 'PUBLISHED')
                                    <a class="btn btn-sm btn-outline-primary ms-2"
                                    href="{{ route('admin.eraport.batches.entries.downloadPdf', [$batch, $e]) }}">
                                        PDF
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary ms-2" disabled title="Publish batch dulu">
                                        PDF
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Entry belum tersedia.</td>
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

<script>
(function(){
    const btn = document.getElementById('btnValidate');
    const box = document.getElementById('validateBox');
    const vStatus = document.getElementById('vStatus');
    const vCount = document.getElementById('vCount');
    const vMissing = document.getElementById('vMissing');

    async function validateBatch(){
        btn.disabled = true;
        btn.innerText = 'Memvalidasi...';

        try {
            const res = await fetch("{{ route('admin.eraport.batches.validate', $batch) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });

            const data = await res.json();

            box.classList.remove('d-none');
            vStatus.innerText = data.batch_status ?? '-';
            vCount.innerText = data.missing_count ?? '-';

            const missing = data.missing ?? {};
            const keys = Object.keys(missing);

            if(keys.length === 0){
                vMissing.innerHTML = '<span class="text-success">✅ Semua data lengkap. Batch siap dipublish.</span>';
            } else {
                let html = '<ul class="mb-0">';
                keys.forEach(uid => {
                    html += `<li><b>User ${uid}</b>: ${missing[uid].join(', ')}</li>`;
                });
                html += '</ul>';
                vMissing.innerHTML = html;
            }
        } catch (e) {
            box.classList.remove('d-none');
            vMissing.innerHTML = '<span class="text-danger">Gagal memvalidasi. Cek response server.</span>';
        } finally {
            btn.disabled = false;
            btn.innerText = 'Validasi Kelengkapan';
        }
    }

    btn.addEventListener('click', validateBatch);
})();
</script>
@endsection
