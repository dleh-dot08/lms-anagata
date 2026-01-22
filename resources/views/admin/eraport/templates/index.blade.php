@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">E-Raport — Template</h4>
            <small class="text-muted">Kelola desain/template e-raport (HTML atau Background Overlay).</small>
        </div>
        <a href="{{ route('admin.eraport.templates.create') }}" class="btn btn-primary">
            + Tambah Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th>Nama</th>
                            <th style="width:130px;">Code</th>
                            <th style="width:160px;">Tipe</th>
                            <th style="width:120px;">Jenjang</th>
                            <th style="width:110px;">Aktif</th>
                            <th style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $t)
                            <tr>
                                <td>{{ $t->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $t->name }}</div>
                                    <small class="text-muted">
                                        @if($t->layout_type === 'html')
                                            View: {{ $t->view_path ?: '-' }}
                                        @else
                                            Background: {{ $t->background_path ?: '-' }}
                                        @endif
                                    </small>
                                </td>
                                <td>{{ $t->code ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $t->layout_type === 'html' ? 'info' : 'secondary' }}">
                                        {{ strtoupper($t->layout_type) }}
                                    </span>
                                </td>
                                <td>{{ $t->jenjang_id ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $t->is_active ? 'success' : 'danger' }}">
                                        {{ $t->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.eraport.templates.edit', $t) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.eraport.templates.destroy', $t) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus template ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada template. Klik “Tambah Template”.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
