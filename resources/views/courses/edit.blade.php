@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Edit Kursus</h2>

    <form action="{{ route('courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Kelas</label>
            <input type="text" name="nama_kelas" class="form-control" value="{{ old('nama_kelas', $course->nama_kelas ?? '') }}" required>
        </div>

        {{-- START: Bagian Mentor Utama dan Cadangan --}}
        <div class="mb-3">
            <label for="mentor_id">Mentor Utama</label>
            <select name="mentor_id" id="mentor_id" class="form-control" required style="width: 100%;">
                {{-- Pastikan mentor utama yang sudah ada terpilih saat halaman dimuat --}}
                @if($course->mentor)
                    <option value="{{ $course->mentor->id }}" selected>{{ $course->mentor->name }}</option>
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label for="mentor_id_2">Mentor Cadangan 1 (Opsional)</label>
            <select name="mentor_id_2" id="mentor_id_2" class="form-control" style="width: 100%;">
                <option value="">-- Pilih Mentor Cadangan 1 --</option> {{-- Opsi default kosong --}}
                {{-- Pastikan mentor cadangan 1 yang sudah ada terpilih saat halaman dimuat --}}
                @if($course->mentor2)
                    <option value="{{ $course->mentor2->id }}" selected>{{ $course->mentor2->name }}</option>
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label for="mentor_id_3">Mentor Cadangan 2 (Opsional)</label>
            <select name="mentor_id_3" id="mentor_id_3" class="form-control" style="width: 100%;">
                <option value="">-- Pilih Mentor Cadangan 2 --</option> {{-- Opsi default kosong --}}
                {{-- Pastikan mentor cadangan 2 yang sudah ada terpilih saat halaman dimuat --}}
                @if($course->mentor3)
                    <option value="{{ $course->mentor3->id }}" selected>{{ $course->mentor3->name }}</option>
                @endif
            </select>
        </div>
        {{-- END: Bagian Mentor Utama dan Cadangan --}}


        <div class="mb-3">
            <label for="sekolah_id">Sekolah</label>
            <select name="sekolah_id" id="sekolah_id" class="form-control">
                <option value="">Pilih Sekolah</option>
                @foreach($sekolah as $s)
                    <option value="{{ $s->id }}" {{ (isset($course) && $course->sekolah_id == $s->id) ? 'selected' : '' }}>
                        {{ $s->nama_sekolah }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori_id" class="form-control">
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" {{ (isset($course) && $course->kategori_id == $kategori->id) ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Program</label>
            <select name="program_id" class="form-control">
            <option value="">Pilih Program</option>
            @foreach ($programs as $program)
                    <option value="{{ $program->id }}" {{ (isset($course) && $course->program_id == $program->id) ? 'selected' : '' }}>
                        {{ $program->nama_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jenjang</label>
            <select name="jenjang_id" id="jenjang_id" class="form-control">
                <option value="">Pilih Jenjang</option>
                @foreach ($jenjangs as $jenjang)
                    <option value="{{ $jenjang->id }}" {{ (isset($course) && $course->jenjang_id == $jenjang->id) ? 'selected' : '' }}>
                        {{ $jenjang->nama_jenjang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Kelas</label>
            <select name="kelas_id" id="kelas_id" class="form-control" {{ !isset($course->jenjang_id) ? 'disabled' : '' }}>
                <option value="">Pilih Kelas</option>
                {{-- Opsi kelas akan diisi oleh JavaScript saat halaman dimuat atau jenjang berubah --}}
            </select>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required>{{ old('deskripsi', $course->deskripsi) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Aktif" {{ (isset($course) && $course->status == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ (isset($course) && $course->status == 'Nonaktif') ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Level</label>
            <select name="level" class="form-control">
                <option value="Beginner" {{ $course->level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="Intermediate" {{ $course->level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="Advanced" {{ $course->level == 'Advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Waktu Mulai</label>
            <input type="datetime-local" name="waktu_mulai" class="form-control"
                value="{{ old('waktu_mulai', \Carbon\Carbon::parse($course->waktu_mulai)->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label>Waktu Akhir</label>
            <input type="datetime-local" name="waktu_akhir" class="form-control"
                value="{{ old('waktu_akhir', \Carbon\Carbon::parse($course->waktu_akhir)->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="{{ old('harga', $course->harga ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Jumlah Peserta</label>
            <input type="number" name="jumlah_peserta" class="form-control" value="{{ old('jumlah_peserta', $course->jumlah_peserta ?? '') }}">
        </div>


        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection

@push('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk Mentor Utama
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

        // Inisialisasi Select2 untuk Mentor Cadangan 1
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

        // Inisialisasi Select2 untuk Mentor Cadangan 2
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

        // Fungsi untuk memuat opsi kelas berdasarkan jenjang
        function loadKelas(jenjangId, selectedKelasId = null) {
            const kelasSelect = $('#kelas_id');
            kelasSelect.empty(); // Kosongkan opsi kelas yang sudah ada
            kelasSelect.append('<option value="">Pilih Kelas</option>'); // Tambahkan opsi default

            if (!jenjangId) {
                kelasSelect.prop('disabled', true);
                return;
            }

            kelasSelect.prop('disabled', false);

            fetch(`/api/jenjang/${jenjangId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Kelas</option>';
                    data.forEach(kelas => {
                        // Periksa apakah kelas saat ini harus dipilih
                        const selected = (selectedKelasId && selectedKelasId == kelas.id) ? 'selected' : '';
                        options += `<option value="${kelas.id}" ${selected}>${kelas.nama}</option>`;
                    });
                    kelasSelect.html(options);
                })
                .catch(error => {
                    console.error('Error fetching kelas:', error);
                    kelasSelect.html('<option value="">Gagal memuat kelas</option>');
                    kelasSelect.prop('disabled', true);
                });
        }

        // Event handler saat jenjang diubah
        $('#jenjang_id').on('change', function() {
            const jenjangId = $(this).val();
            // Saat jenjang berubah, kita tidak ingin menjaga pilihan kelas sebelumnya
            loadKelas(jenjangId);
        });

        // Saat dokumen siap (halaman dimuat), muat kelas berdasarkan jenjang yang sudah ada di $course
        const initialJenjangId = $('#jenjang_id').val();
        const initialKelasId = "{{ $course->kelas_id ?? '' }}"; // Ambil kelas_id dari objek course
        if (initialJenjangId) {
            loadKelas(initialJenjangId, initialKelasId);
        }
    });
</script>
@endpush