@extends('layouts.admin.template')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3">Tambah Peserta ke Kursus: {{ $course->nama_kelas }}</h4>

        <form method="GET" action="{{ route('courses.formparticipant', $course->id) }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="jenjang_id" class="form-label">Filter Jenjang</label>
                    <select name="jenjang_id" id="jenjang_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Jenjang --</option>
                        @foreach($jenjangs as $jenjang)
                            <option value="{{ $jenjang->id }}" {{ $filterJenjang == $jenjang->id ? 'selected' : '' }}>
                                {{ $jenjang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <form action="{{ route('courses.participants.store', $course->id) }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenjang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->jenjang->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada peserta ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Tambah Peserta Terpilih</button>
                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        let checkboxes = document.querySelectorAll('input[name="user_ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
