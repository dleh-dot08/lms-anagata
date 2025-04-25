@extends('layouts.karyawan.template')

@section('content')
    <div class="container mt-4">
    <h1>FAQ Management</h1>
        <a href="{{ route('faq.mrc.create') }}" class="btn btn-primary">Create New FAQ</a>

        <form method="GET" action="{{ route('faq.mrc.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari pertanyaan..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <table class="table table-hover table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faqs as $faq)
                <tr>
                    <td>{{ Str::limit($faq->question, 50) }}</td>
                    <td>{{ $faq->category }}</td>
                    <td>{{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    <td>{{ $faq->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('faq.mrc.show', $faq->id) }}" class="btn btn-info btn-sm">Lihat</a>
                        <a href="{{ route('faq.mrc.edit', $faq->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('faq.mrc.destroy', $faq->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-4">
            {{ $faqs->onEachSide(1)->links('pagination.custom') }}
        </div>


    </div>
@endsection
