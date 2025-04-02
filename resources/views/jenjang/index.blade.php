@extends('layouts.admin.template')

@section('content')
    <h2>Daftar Jenjang</h2>
    <a href="{{ route('jenjang.create') }}" class="btn btn-primary mb-3">Tambah Jenjang</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Jenjang</th>
                <th>Deskripsi</th>
                <th>Deleted At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jenjangs as $index => $jenjang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jenjang->nama_jenjang }}</td>
                    <td>{{ $jenjang->description }}</td>
                    <td>{{ $jenjang->deleted_at ?? 'Aktif' }}</td>
                    <td>
                        <a href="{{ route('jenjang.show', $jenjang->id) }}" class="btn btn-info btn-sm">Detail</a>
                        @if(!$jenjang->deleted_at)
                            <a href="{{ route('jenjang.edit', $jenjang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('jenjang.destroy', $jenjang->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        @else
                            <form action="{{ route('jenjang.restore', $jenjang->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $jenjangs->links() }}
@endsection
