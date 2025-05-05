@extends('layouts.karyawan.template')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Tambah Peserta ke Kegiatan: {{ $activity->nama_kegiatan }}</h4>
            <a href="{{ route('activities.apd.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control" placeholder="Cari nama atau email..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <select name="perPage" class="form-select">
                    @foreach ([10, 50, 100, 'all'] as $num)
                        <option value="{{ $num }}" {{ request('perPage') == $num ? 'selected' : '' }}>
                            {{ is_numeric($num) ? "$num per halaman" : 'Tampilkan Semua' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>

        <form action="{{ route('activities.apd.participants.store', $activity->id) }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenjang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($participants as $participant)
                        <tr>
                            <td><input type="checkbox" name="user_ids[]" value="{{ $participant->id }}"></td>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->email }}</td>
                            <td>{{ $participant->jenjang->nama_jenjang ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada peserta tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <button type="submit" class="btn btn-success mt-2">Tambah Peserta Terpilih</button>
        </form>
        @if($perPage !== 'all')
            <div class="d-flex justify-content-center mt-4">
                {{ $participants->onEachSide(1)->links('pagination.custom') }}
            </div>
        @endif
    </div>
</div>

<script>
    document.getElementById('select-all').onclick = function () {
        let checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    };
</script>
@endsection
