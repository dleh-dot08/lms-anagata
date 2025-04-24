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
                <label for="mentor_id">Mentor</label>
                <select name="mentor_id" id="mentor_id" class="form-control" required style="width: 100%;"></select>
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
                <label for="jenjang_id">Jenjang</label>
                <select name="jenjang_id" id="jenjang_id" class="form-control" required>
                    <option value="">Pilih Jenjang</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang->id }}">{{ $jenjang->nama_jenjang }}</option>
                    @endforeach
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
        $('#mentor_id').select2({
            placeholder: 'Cari Mentor',
            allowClear: true,
            width: 'resolve',
            minimumInputLength: 1, // ✅ only start searching after 1 character
            ajax: {
                url: @json(route('courses.searchMentor')),
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
    });

</script>
@endpush
