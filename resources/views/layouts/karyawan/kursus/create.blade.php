@extends('layouts.karyawan.template')

@section('content')
    <div class="container mt-4">
        <h2>Tambah Kursus Baru</h2>

        <form action="{{ route('courses.apd.store') }}" method="POST">
            @csrf
            @include('layouts.karyawan.kursus.form')
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
                        options += `<option value="${kelas.id}">${kelas.nama}</option>`;
                    });
                    kelasSelect.html(options);
                });
        });
    });
</script>
@endpush