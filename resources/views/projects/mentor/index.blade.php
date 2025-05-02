<!-- resources/views/projects/mentor/index.blade.php -->
@extends('layouts.mentor.template')

@section('content')
<div class="container">
    <h1>Daftar Project Peserta</h1>
    
    @if($projects->isEmpty())
        <p>Tidak ada project yang ditemukan.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Peserta</th>
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
                        <td>{{ $project->user->name }}</td>
                        <td>{{ $project->course->name }}</td>
                        <td>{{ $project->created_at->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('mentor.projects.show', $project->id) }}" class="btn btn-info btn-sm">Lihat</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $projects->onEachSide(1)->links('pagination.custom') }}
        </div>
    @endif
</div>
@endsection
