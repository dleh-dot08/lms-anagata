@extends('layouts.admin.template')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3">Tambah Peserta ke Kursus: {{ $course->nama_kelas }}</h4>

        <form method="GET" class="row g-2 mb-3 align-items-end"> {{-- Tambah align-items-end agar tombol sejajar --}}
            <div class="col-md-3 col-lg-2"> {{-- Kolom lebih kecil untuk dropdown --}}
                <label for="jenjang-filter" class="form-label visually-hidden">Filter Jenjang</label>
                <select name="jenjang" id="jenjang-filter" class="form-select">
                    <option value="">-- Semua Jenjang --</option>
                    @foreach ($jenjangList as $jenjang)
                        <option value="{{ $jenjang->id }}" {{ request('jenjang') == $jenjang->id ? 'selected' : '' }}>
                            {{ $jenjang->nama_jenjang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-2"> {{-- Kolom baru untuk Sekolah --}}
                <label for="sekolah-filter" class="form-label visually-hidden">Filter Sekolah</label>
                <select name="sekolah" id="sekolah-filter" class="form-select">
                    <option value="">-- Semua Sekolah --</option>
                    @foreach ($sekolahList as $sekolah)
                        <option value="{{ $sekolah->id }}" {{ request('sekolah') == $sekolah->id ? 'selected' : '' }}>
                            {{ $sekolah->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-2"> {{-- Kolom baru untuk Kelas --}}
                <label for="kelas-filter" class="form-label visually-hidden">Filter Kelas</label>
                <select name="kelas" id="kelas-filter" class="form-select">
                    <option value="">-- Semua Kelas --</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama ?? $kelas->nama_kelas }} {{-- Sesuaikan jika kolom nama kelas berbeda --}}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-lg-3"> {{-- Kolom untuk search query --}}
                <label for="search-input" class="form-label visually-hidden">Cari</label>
                <input type="text" name="q" id="search-input" class="form-control" placeholder="Cari nama atau email..." value="{{ request('q') }}">
            </div>

            <div class="col-md-2 col-lg-1"> {{-- Kolom untuk perPage --}}
                <label for="perPage-select" class="form-label visually-hidden">Per Halaman</label>
                <select name="perPage" id="perPage-select" class="form-select">
                    @foreach ([10, 50, 100, 'all'] as $num)
                        <option value="{{ $num }}" {{ request('perPage') == $num ? 'selected' : '' }}>
                            {{ is_numeric($num) ? "$num per halaman" : 'Semua' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-auto"> {{-- Kolom untuk tombol filter --}}
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>

        <form action="{{ route('courses.participants.store', $course->id) }}" method="POST">
        @csrf
        <div class="table-responsive"> {{-- Tambahkan wrapper table-responsive --}}
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenjang</th>
                        <th>Sekolah</th> {{-- Kolom baru --}}
                        <th>Kelas</th> {{-- Kolom baru --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($participants as $participant)
                        <tr>
                            <td><input type="checkbox" name="user_ids[]" value="{{ $participant->id }}"></td>
                            <td>{{ $participant->name }}</td>
                            <td>{{ $participant->email }}</td>
                            <td>{{ $participant->jenjang->nama_jenjang ?? '-' }}</td>
                            <td>{{ $participant->sekolah->nama_sekolah ?? '-' }}</td> {{-- Tampilkan nama sekolah --}}
                            <td>{{ $participant->kelas->nama ?? '-' }}</td> {{-- Tampilkan nama kelas --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada peserta tersedia.</td> {{-- Sesuaikan colspan --}}
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div> {{-- End table-responsive --}}

        <button type="submit" class="btn btn-success mt-3">Tambah Peserta Terpilih</button>
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