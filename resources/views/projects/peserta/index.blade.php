@extends('layouts.peserta.template')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Project Saya</h5>
            <a href="{{ route('projects.peserta.create') }}" class="btn btn-light btn-sm">+ Buat Project Baru</a>
        </div>
        <div class="card-body">
            @if($projects->isEmpty())
                <div class="alert alert-info text-center" role="alert">
                    Tidak ada project yang ditemukan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Judul</th>
                                <th scope="col">Kursus</th>
                                <th scope="col">Tanggal Dibuat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $project->title }}</td>
                                    <td>{{ optional($project->course)->nama_kelas ?? '-' }}</td>
                                    <td class="text-center">{{ $project->created_at->format('d-m-Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('projects.peserta.show', $project->id) }}" class="btn btn-info btn-sm mb-1">Lihat</a>
                                        <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                                        <form action="{{ route('projects.peserta.destroy', $project->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin ingin menghapus project ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
