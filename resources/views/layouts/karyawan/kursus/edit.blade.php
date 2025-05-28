@extends('layouts.karyawan.template')

@section('content')
<div class="container mt-4">
    <h2>Edit Kursus</h2>

    <form action="{{ route('courses.apd.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('layouts.karyawan.kursus.form', ['course' => $course])
        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
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
