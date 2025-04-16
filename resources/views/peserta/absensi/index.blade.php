@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h4 class="mb-4">Daftar Kelas yang Belum Diabsen Hari Ini</h4>
    <table class="table table-bordered">
        <thead>
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
            @forelse ($courses as $index => $course)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $course->nama }}</td>
                    <td>{{ $course->mentor->name ?? '-' }}</td>
                    <td>{{ $course->kategori }}</td>
                    <td>{{ $course->jenjang }}</td>
                    <td>Belum Absen</td>
                    <td>
                        <a href="{{ route('attendances.create', $course->id) }}" class="btn btn-sm btn-primary">Absen</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Semua kelas sudah diabsen hari ini 🎉</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
