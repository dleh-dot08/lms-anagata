@extends('layouts.admin.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Admin / Sekolah /</span> Edit Sekolah
    </h4>

    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.sekolah.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Form Edit Sekolah Card -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Form Edit Sekolah</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sekolah.update', $sekolah->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama_sekolah">Nama Sekolah</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" 
                                       id="nama_sekolah" name="nama_sekolah" 
                                       value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" 
                                       placeholder="Masukkan nama sekolah" required>
                                @error('nama_sekolah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jenjang_id">Jenjang</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('jenjang_id') is-invalid @enderror" 
                                        id="jenjang_id" name="jenjang_id" required>
                                    <option value="">Pilih Jenjang</option>
                                    @foreach($jenjang as $j)
                                        <option value="{{ $j->id }}" 
                                            {{ old('jenjang_id', $sekolah->jenjang_id) == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama_jenjang }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenjang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="alamat">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                          id="alamat" name="alamat" rows="3" 
                                          placeholder="Masukkan alamat sekolah" required>{{ old('alamat', $sekolah->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pic_id">PIC Sekolah</label>
                            <div class="col-sm-10">
                                <select class="form-select @error('pic_id') is-invalid @enderror" 
                                        id="pic_id" name="pic_id">
                                    <option value="">Pilih PIC Sekolah</option>
                                    @foreach($available_pics as $pic)
                                        <option value="{{ $pic->id }}" 
                                            {{ old('pic_id', optional($sekolah->pic)->id) == $pic->id ? 'selected' : '' }}>
                                            {{ $pic->name }} ({{ $pic->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pic_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih user dengan role Sekolah yang belum ditugaskan ke sekolah lain</small>
                            </div>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.sekolah.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Assign Peserta Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Assign Peserta</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sekolah.update-peserta', $sekolah->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="searchPeserta" 
                                           placeholder="Cari nama atau email peserta...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select" id="kelasFilter">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelas->where('id_jenjang', $sekolah->jenjang_id) as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Kelas</th>
                                        <th>Sekolah Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($available_peserta as $peserta)
                                        <tr data-kelas="{{ $peserta->kelas_id }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input peserta-checkbox" 
                                                       name="peserta_ids[]" value="{{ $peserta->id }}"
                                                       {{ $peserta->sekolah_id == $sekolah->id ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $peserta->name }}</td>
                                            <td>{{ $peserta->email }}</td>
                                            <td>{{ $peserta->kelas ? $peserta->kelas->nama : '-' }}</td>
                                            <td>
                                                @if($peserta->sekolah_id && $peserta->sekolah_id != $sekolah->id)
                                                    <span class="badge bg-label-info">{{ $peserta->sekolah->nama_sekolah }}</span>
                                                @elseif($peserta->sekolah_id == $sekolah->id)
                                                    <span class="badge bg-label-success">Sekolah Ini</span>
                                                @else
                                                    <span class="badge bg-label-warning">Belum Ada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-user-check me-1"></i> Update Peserta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All functionality
        const selectAll = document.getElementById('selectAll');
        const pesertaCheckboxes = document.querySelectorAll('.peserta-checkbox');

        selectAll.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('tr:not([style*="display: none"]) .peserta-checkbox');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });

        // Search and Filter functionality
        const searchInput = document.getElementById('searchPeserta');
        const kelasFilter = document.getElementById('kelasFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedKelas = kelasFilter.value;

            tableRows.forEach(row => {
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const kelasId = row.dataset.kelas;
                
                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesKelas = !selectedKelas || kelasId === selectedKelas;

                row.style.display = (matchesSearch && matchesKelas) ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTable);
        kelasFilter.addEventListener('change', filterTable);
    });
</script>
@endpush
@endsection
