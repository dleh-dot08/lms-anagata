<!-- resources/views/projects/peserta/index.blade.php -->
@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h1>Daftar Project Saya</h1>
    <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">Buat Project Baru</a>

    @if($projects->isEmpty())
        <p>Tidak ada project yang ditemukan.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Kursus</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $project->title }}</td>
                        <td>{{ $project->course->name }}</td>
                        <td>{{ $project->created_at->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-info btn-sm">Lihat</a>
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        {{ $projects->links() }}
    @endif
</div>
@endsection
