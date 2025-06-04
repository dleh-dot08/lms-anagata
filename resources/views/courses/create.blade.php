@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Tambah Kursus Baru</h2>

        <form action="{{ route('courses.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama_kelas">Nama Kelas</label>
                <input type="text" name="nama_kelas" id="nama_kelas" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
            </div>

            {{-- START: Tambahan untuk Mentor Cadangan --}}
            <div class="form-group">
                <label for="mentor_id">Mentor Utama</label>
                <select name="mentor_id" id="mentor_id" class="form-control" required style="width: 100%;">
                    {{-- Opsi untuk mentor utama akan dimuat oleh Select2 --}}
                </select>
            </div>

            <div class="form-group">
                <label for="mentor_id_2">Mentor Cadangan 1 (Opsional)</label>
                <select name="mentor_id_2" id="mentor_id_2" class="form-control" style="width: 100%;">
                    {{-- Opsi untuk mentor cadangan 1 akan dimuat oleh Select2 --}}
                    <option value="">-- Pilih Mentor Cadangan 1 --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mentor_id_3">Mentor Cadangan 2 (Opsional)</label>
                <select name="mentor_id_3" id="mentor_id_3" class="form-control" style="width: 100%;">
                    {{-- Opsi untuk mentor cadangan 2 akan dimuat oleh Select2 --}}
                    <option value="">-- Pilih Mentor Cadangan 2 --</option>
                </select>
            </div>
            {{-- END: Tambahan untuk Mentor Cadangan --}}

            <div class="form-group">
                <label for="sekolah_id">Sekolah</label>
                <select name="sekolah_id" id="sekolah_id" class="form-control">
                    <option value="">Pilih Sekolah</option>
                    @foreach($sekolah as $s)
                        <option value="{{ $s->id }}" {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="kategori_id">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="form-control" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="program_id">Program</label>
                <select name="program_id" id="program_id" class="form-control" required>
                    <option value="">Pilih Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->nama_program }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jenjang_id">Jenjang</label>
                <select name="jenjang_id" id="jenjang_id" class="form-control" required>
                    <option value="">Pilih Jenjang</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang->id }}">{{ $jenjang->nama_jenjang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="form-control" disabled>
                    <option value="">Pilih Kelas</option>
                </select>
            </div>

            <div class="form-group">
                <label for="level">Level</label>
                <select name="level" id="level" class="form-control" required>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="form-group">
                <label for="waktu_mulai">Waktu Mulai</label>
                <input type="date" name="waktu_mulai" id="waktu_mulai" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="waktu_akhir">Waktu Akhir</label>
                <input type="date" name="waktu_akhir" id="waktu_akhir" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" class="form-control">
            </div>

            <div class="form-group">
                <label for="jumlah_peserta">Jumlah Peserta</label>
                <input type="number" name="jumlah_peserta" id="jumlah_peserta" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection
@push('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // --- Inisialisasi Select2 untuk ketiga mentor ---

        // Mentor Utama
        $('#mentor_id').select2({
            placeholder: 'Cari Mentor Utama',
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('courses.searchMentor') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        // Mentor Cadangan 1
        $('#mentor_id_2').select2({
            placeholder: 'Cari Mentor Cadangan 1',
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('courses.searchMentor') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        // Mentor Cadangan 2
        $('#mentor_id_3').select2({
            placeholder: 'Cari Mentor Cadangan 2',
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 1,
            ajax: {
                url: "{{ route('courses.searchMentor') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return { results: data };
                }
            }
        });

        // --- Jenjang change event handler (tetap sama) ---
        $('#jenjang_id').on('change', function() {
            const jenjangId = $(this).val();
            const kelasSelect = $('#kelas_id');

            if (!jenjangId) {
                kelasSelect.html('<option value="">Pilih Kelas</option>');
                kelasSelect.prop('disabled', true);
                return;
            }

            kelasSelect.prop('disabled', false);

            fetch(`/api/jenjang/${jenjangId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Kelas</option>';
                    data.forEach(kelas => {
                        options += `<option value="${kelas.id}">${kelas.nama}</option>`;
                    });
                    kelasSelect.html(options);
                })
                .catch(error => {
                    console.error('Error fetching kelas:', error);
                    kelasSelect.html('<option value="">Gagal memuat kelas</option>');
                    kelasSelect.prop('disabled', true);
                });
        });

        // Trigger change event on page load if jenjang_id has a value (for edit page)
        if ($('#jenjang_id').val()) {
            $('#jenjang_id').trigger('change');
        }
    });
</script>
@endpush