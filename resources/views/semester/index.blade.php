@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h2>Daftar Semester</h2>
    <a href="{{ route('semester.create') }}" class="btn btn-primary mb-3">Tambah Semester</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tahun</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesters as $semester)
            <tr>
                <td>{{ $semester->name }}</td>
                <td>{{ $semester->year }}</td>
                <td>
                    @if($semester->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-secondary">Tidak Aktif</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('semester.edit', $semester->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('semester.destroy', $semester->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                    @if(!$semester->is_active)
                        <form action="{{ route('semester.update', $semester->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_active" value="1">
                            <button class="btn btn-sm btn-success" onclick="return confirm('Jadikan semester aktif?')">Aktifkan</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection