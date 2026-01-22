@extends('layouts.sekolah.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">E-Raport Peserta Sekolah</h4>
            <small class="text-muted">Sekolah bisa mengunduh e-raport peserta yang terdaftar di sekolah.</small>
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
                        <th>Peserta</th>
                        <th>Program / Kursus</th>
                        <th style="width:170px;">Semester</th>
                        <th style="width:190px;">Nomor Raport</th>
                        <th style="width:200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eraports as $r)
                        @php
                            $snap = is_array($r->snapshot_json) ? $r->snapshot_json : (json_decode($r->snapshot_json, true) ?: []);
                            $studentName = data_get($snap,'nama','User #'.$r->user_id);
                            $courseName = data_get($snap,'course.nama_kelas','-');
                            $semester = data_get($snap,'semester_label','-');
                        @endphp
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $studentName }}</div>
                                <small class="text-muted">user_id: {{ $r->user_id }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $courseName }}</div>
                                <small class="text-muted">Batch #{{ $r->batch_id }} | Versi {{ $r->version }}</small>
                            </td>
                            <td>{{ $semester }}</td>
                            <td class="small">{{ $r->report_number }}</td>
                            <td class="d-flex flex-wrap gap-2">
                                <a href="{{ route('sekolah.eraport.download', $r) }}"
                                   class="btn btn-sm btn-outline-primary {{ $r->pdf_path ? '' : 'disabled' }}">
                                    Download PDF
                                </a>

                                @if(!empty($r->verify_token))
                                    <a href="{{ route('public.eraport.verify', ['token' => $r->verify_token]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-success">
                                        Verifikasi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada e-raport yang bisa diunduh.
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
