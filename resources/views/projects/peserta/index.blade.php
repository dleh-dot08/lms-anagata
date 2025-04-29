@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <div class="">
        <h3 class="fw-bold py-3 mb-1 mt-2">Daftar Project Anda</h3>
        <div class="card shadow-sm p-4">

            <!-- Menampilkan Pesan Error -->
            @if(session('errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tombol Buat Project Baru -->
            <div class="mb-3">
                <a href="{{ route('projects.peserta.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Buat Project Baru
                </a>
            </div>

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('projects.peserta.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari project...">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </div>
            </form>

            <!-- Tabel Project -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Project</th>
                        <th>Kursus</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $project->title }}</td>
                        <td>{{ $project->course->nama_kelas ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('projects.peserta.show', $project->id) }}" class="btn btn-info btn-sm">Lihat</a>
                            <a href="{{ route('projects.peserta.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada project.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection
