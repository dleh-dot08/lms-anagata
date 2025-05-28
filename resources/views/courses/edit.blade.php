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

        <div class="mb-3">
            <label for="mentor_id">Mentor</label>
            <select name="mentor_id" id="mentor_id" class="form-control" required style="width: 100%;">
            @if($course->mentor)
                <option value="{{ $course->mentor->id }}" selected>{{ $course->mentor->name }}</option>
            @endif
</select>

        </div>

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
                @if(isset($course->jenjang_id))
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ (isset($course) && $course->kelas_id == $k->id) ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                @endif
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
        // Mentor Select2 initialization
        $('#mentor_id').select2({
            placeholder: 'Cari Mentor',
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

        // Jenjang change event handler
        $('#jenjang_id').on('change', function() {
            const jenjangId = $(this).val();
            const kelasSelect = $('#kelas_id');
            
            // Reset and disable kelas select if no jenjang selected
            if (!jenjangId) {
                kelasSelect.html('<option value="">Pilih Kelas</option>');
                kelasSelect.prop('disabled', true);
                return;
            }

            // Enable kelas select
            kelasSelect.prop('disabled', false);

            // Fetch kelas options based on selected jenjang
            fetch(`/api/jenjang/${jenjangId}/kelas`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Pilih Kelas</option>';
                    data.forEach(kelas => {
                        const selected = "{{ $course->kelas_id }}" == kelas.id ? 'selected' : '';
                        options += `<option value="${kelas.id}" ${selected}>${kelas.nama}</option>`;
                    });
                    kelasSelect.html(options);
                });
        });

        // Trigger jenjang change event if there's a selected value
        if ($('#jenjang_id').val()) {
            $('#jenjang_id').trigger('change');
        }
    });
</script>
@endpush