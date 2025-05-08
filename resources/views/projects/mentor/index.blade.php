@extends('layouts.mentor.template')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📂 Daftar Project Peserta</h2>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('mentor.projects.index') }}" method="GET" class="row gy-2 gx-3 align-items-end mb-4">
        <div class="col-sm-4">
            <label for="course_id" class="form-label">Filter Kursus</label>
            <select name="course_id" id="course_id" class="form-select">
                <option value="">Semua Kursus</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">🔍 Cari</button>
        </div>
    </form>

    @if($projects->isEmpty())
        <div class="alert alert-warning text-center">
            Tidak ada project yang ditemukan untuk filter ini.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle shadow-sm">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width: 50px;">#</th>
                        <th>Judul</th>
                        <th>Peserta</th>
                        <th>Kursus</th>
                        <th>Tanggal Dibuat</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $index => $project)
                        <tr>
                            <td class="text-center">
                                {{ ($projects->currentPage() - 1) * $projects->perPage() + $index + 1 }}
                            </td>
                            <td>{{ $project->title }}</td>
                            <td>{{ $project->user->name }}</td>
                            <td>{{ $project->course->nama_kelas }}</td>
                            <td>{{ $project->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('mentor.projects.show', $project->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $projects->onEachSide(1)->links('pagination.custom') }}
        </div>
    @endif
</div>
@endsection
