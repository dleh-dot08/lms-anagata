@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kelas</h2>
            <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary" style="background-color: #6777EF;">
                Tambah Kelas
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Kelas</th>
                                <th>Jenjang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kelas as $index => $item)
                                <tr>
                                    <td>{{ $kelas->firstItem() + $index }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ optional($item->jenjang)->nama_jenjang }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.kelas.show', $item->id) }}" 
                                                class="btn btn-info btn-sm text-white">Detail
                                            </a>
                                            <a href="{{ route('admin.kelas.edit', $item->id) }}" 
                                                class="btn btn-warning btn-sm text-white">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.kelas.destroy', $item->id) }}" 
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    {{ $kelas->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection 