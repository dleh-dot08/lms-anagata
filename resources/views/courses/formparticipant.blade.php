@extends('layouts.admin.template')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4>Tambah Peserta ke Kursus: {{ $course->nama_kelas }}</h4>

        <form action="{{ route('courses.participants.store', $course->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="user_ids">Pilih Peserta (bisa banyak)</label>
                <select name="user_ids[]" id="user_ids" class="form-control" multiple style="width: 100%">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Tambahkan Peserta</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#user_ids').select2({
            placeholder: "Cari dan pilih peserta...",
            ajax: {
                url: '{{ route("courses.participants.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
    </script>
@endpush
