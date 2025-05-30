@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Daftar Kursus</h2>

    <!-- Tabs for Aktif and Nonaktif -->
    <ul class="nav nav-tabs" id="courseTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link @if(request('tab') != 'nonaktif') active @endif" id="active-tab" href="{{ route('courses.index', ['tab' => 'aktif']) }}">Kursus Aktif</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link @if(request('tab') == 'nonaktif') active @endif" id="inactive-tab" href="{{ route('courses.index', ['tab' => 'nonaktif']) }}">Kursus Tidak Aktif</a>
        </li>
    </ul>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('courses.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Kursus" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </div>
    </form>

    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Tambah Kursus</a>

    <table class="table table-hover table-bordered">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Nama Kelas</th>
                <th>Mentor</th>
                <th>Kategori</th>
                <th>Jenjang</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
            <tr @if($course->trashed()) style="text-decoration: line-through;" @endif>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $course->nama_kelas }}</td>
                <td>{{ $course->mentor->name ?? 'Tidak Ada' }}</td>
                <td>{{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</td>
                <td>{{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</td>
                <td>
                    @php
                        $isExpired = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($course->waktu_akhir));
                        $finalStatus = $isExpired && $course->status == 'Aktif' ? 'Nonaktif' : $course->status;
                    @endphp

                    <span class="badge {{ $finalStatus == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                        {{ $finalStatus }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-info btn-sm">Lihat</a>
                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    
                    @if($course->trashed())
                        <form action="{{ route('courses.restore', $course->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Pulihkan</button>
                        </form>
                    @else
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $courses->onEachSide(1)->links('pagination.custom') }}
    </div>
</div>
@endsection
