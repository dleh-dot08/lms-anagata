@extends('layouts.peserta.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">E-Raport Saya</h4>
            <small class="text-muted">Daftar rapor yang sudah diterbitkan.</small>
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
                        <th>Program / Kursus</th>
                        <th style="width:170px;">Semester</th>
                        <th style="width:190px;">Nomor Raport</th>
                        <th style="width:170px;">Diterbitkan</th>
                        <th style="width:220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eraports as $r)
                        @php
                            $snap = is_array($r->snapshot_json) ? $r->snapshot_json : (json_decode($r->snapshot_json, true) ?: []);
                            $courseName = data_get($snap, 'course.nama_kelas', '-');
                            $semester = data_get($snap, 'semester_label', '-');
                        @endphp
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $courseName }}</div>
                                <small class="text-muted">Batch: #{{ $r->batch_id }} | Versi: {{ $r->version }}</small>
                            </td>
                            <td>{{ $semester }}</td>
                            <td class="small">{{ $r->report_number }}</td>
                            <td>
                                <div>{{ optional($r->published_at)->format('d M Y H:i') ?? '-' }}</div>
                                <small class="text-muted">{{ $r->status }}</small>
                            </td>
                            <td class="d-flex flex-wrap gap-2">
                                <a href="{{ route('peserta.eraport.show', $r) }}" class="btn btn-sm btn-outline-primary">
                                    Lihat
                                </a>

                                <a href="{{ route('peserta.eraport.download', $r) }}"
                                   class="btn btn-sm btn-outline-secondary {{ $r->pdf_path ? '' : 'disabled' }}">
                                    Download PDF
                                </a>

                                @if(!empty($r->verify_token))
                                    <a href="{{ route('public.eraport.verify', ['token' => $r->verify_token]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-success">
                                        Link Verifikasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada e-raport yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $eraports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
