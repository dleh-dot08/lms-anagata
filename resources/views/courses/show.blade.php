@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Detail Kursus</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>{{ $course->nama_kelas }}</h3>
            <p><strong>Mentor:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
            <p><strong>Kategori:</strong> {{ $course->kategori_id->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Jenjang:</strong> {{ $course->jenjang_id->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $course->level }}</p>
            <p><strong>Status:</strong> {{ $course->status }}</p>
            <p><strong>Deskripsi:</strong> {{ $course->deskripsi }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $course->waktu_mulai }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $course->waktu_akhir }}</p>
            <p><strong>Harga:</strong> {{ $course->harga ?? 'Gratis' }}</p>
            <p><strong>Jumlah Peserta:</strong> {{ $course->jumlah_peserta }}</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-3">Daftar Peserta</h4>
                <a href="{{ route('courses.participants.form', $course->id) }}" class="btn btn-success">
                    + Tambah Peserta
                </a>
            </div>

            <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nama Peserta</th>
                                <th>Jenjang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($enrollments as $index => $enrollment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $enrollment->user->name }}</td>
                                    <td>{{ $enrollment->user->jenjang->nama ?? '-' }}</td>
                                    <td>
                                        @if ($enrollment->tanggal_selesai)
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-warning">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('courses.participants.destroy', ['course' => $course->id, 'user' => $enrollment->user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada peserta</td>
                                </tr>
                            @endforelse
                        </tbody>
            </table>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <a href="{{ route('courses.createLessonForm', $course->id) }}" class="btn btn-primary mb-3">Tambah Materi Pembelajaran</a>
            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Pertemuan Ke</th>
                        <th>Video</th>
                        <th>File</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->lessons as $lesson)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>Pertemuan {{ $lesson->pertemuan_ke }}</td>
                            <td>
                                @for ($i = 1; $i <= 3; $i++)
                                    @if($lesson["video_url$i"])
                                        <a href="{{ $lesson["video_url$i"] }}" target="_blank">Video {{ $i }}</a><br>
                                    @endif
                                @endfor
                            </td>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($lesson["file_materi$i"])
                                        <a href="{{ asset('storage/' . $lesson["file_materi$i"]) }}" target="_blank">File {{ $i }}</a><br>
                                    @endif
                                @endfor
                            </td>
                            <td>
                                <a href="{{ route('courses.editLesson', [$course->id, $lesson->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                <a href="{{ route('courses.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-info">Lihat</a>
                                <form action="{{ route('courses.deleteLesson', [$course->id, $lesson->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
