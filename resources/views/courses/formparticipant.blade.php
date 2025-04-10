@extends('layouts.admin.template')

@section('content')
    <h4>Tambah Peserta ke: {{ $course->nama_kelas }}</h4>

    <form action="{{ route('courses.participants.store', $course->id) }}" method="POST">
        @csrf
        <label for="user_id">Peserta:</label>
        <select name="user_id" id="user_id" class="form-select" style="width: 100%" required></select>

        <button type="submit" class="btn btn-primary mt-2">Tambah</button>
    </form>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#user_id').select2({
                    placeholder: 'Cari peserta...',
                    ajax: {
                        url: '{{ route("courses.participants.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            });
        </script>
    @endpush
@endsection
