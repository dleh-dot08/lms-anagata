@extends('layouts.admin.template')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="mb-3">Tambah Peserta</h4>

        <!-- Form Tambah Satu Peserta -->
        <form action="{{ route('participants.add', $course->id) }}" method="POST" class="row g-3 mb-4">
            @csrf
            <div class="col-md-6">
                <label for="user_id" class="form-label">Cari Peserta</label>
                <select name="user_ids[]" id="user_id" class="form-select" style="width: 100%" required></select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <!-- Bulk Add -->
        <form action="{{ route('participants.add', $course->id) }}" method="POST" class="mb-4">
            @csrf
            <h5>Bulk Tambah Peserta</h5>
            <div class="mb-2">
                <select name="user_ids[]" class="form-select" multiple required style="height: 300px;">
                    @foreach($allPeserta as $peserta)
                        <option value="{{ $peserta->id }}">{{ $peserta->name }} - {{ $peserta->email }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">Tambah Semua</button>
        </form>

        <!-- Daftar Peserta -->
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Jenjang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments as $index => $enrollment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $enrollment->user->name }}</td>
                        <td>{{ $enrollment->user->jenjang->nama ?? '-' }}</td>
                        <td>
                            @if ($enrollment->tanggal_selesai)
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-warning">Aktif</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('participants.remove', [$course->id, $enrollment->user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Belum ada peserta</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('#user_id').select2({
        placeholder: 'Cari peserta berdasarkan nama/email...',
        ajax: {
            url: '{{ route("participants.search") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data }),
            cache: true
        }
    });
</script>
@endpush
@endsection
