@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif
    <h2>Detail Kursus</h2>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>{{ $course->nama_kelas }}</h3>
            <p><strong>Kode Unik:</strong> {{ $course->kode_unik ?? 'Tidak Ada' }}</p>
            <p><strong>Mentor:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
            <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</p>
            <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $course->level }}</p>
            <p><strong>Status:</strong> {{ $course->status }}</p>
            <p><strong>Deskripsi:</strong> {{ $course->deskripsi }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $course->waktu_mulai }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $course->waktu_akhir }}</p>
            <p><strong>Harga:</strong> {{ $course->harga ?? 'Gratis' }}</p>
            <p><strong>Jumlah Peserta:</strong> {{ $course->jumlah_peserta }}</p>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Daftar Peserta</h4>
                <a href="{{ route('courses.formparticipant', $course->id) }}" class="btn btn-success">
                    + Tambah Peserta
                </a>
            </div>

            <!-- Form pencarian -->
            <form method="GET" action="{{ route('courses.show', $course->id) }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari peserta..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Persentase Kehadiran</th> <!-- Kolom baru untuk persentase -->
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $index => $enrollment)
                        <tr>
                            <td>{{ ($enrollments->currentPage() - 1) * $enrollments->perPage() + $loop->iteration }}</td>
                            <td>{{ $enrollment->user->name }}</td>
                            <td>{{ $enrollment->user->jenjang->nama_jenjang ?? '-' }}</td>
                            <td>
                                @if ($enrollment->tanggal_selesai)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <!-- Menghitung persentase kehadiran -->
                                @php
                                    // Menghitung jumlah pertemuan total
                                    $totalLessons = \App\Models\Lesson::where('course_id', $course->id)->count();
                                    // Menghitung jumlah hadir atau izin
                                    $presentCount = \App\Models\Attendance::where('course_id', $course->id)
                                        ->where('user_id', $enrollment->user->id)
                                        ->whereIn('status', ['Hadir', 'Izin'])
                                        ->count();
                                    // Menghitung persentase kehadiran
                                    $attendancePercentage = $totalLessons > 0 ? ($presentCount / $totalLessons) * 100 : 0;
                                @endphp
                                {{ number_format($attendancePercentage, 2) }}%
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

            <!-- Paginasi -->
            <div class="d-flex justify-content-center">
                {{ $enrollments->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Materi -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <a href="{{ route('courses.admin.createLessonForm', $course->id) }}" class="btn btn-primary mb-3">Tambah Materi Pembelajaran</a>
            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Pertemuan Ke</th>
                        <th>Video</th>
                        <th>File</th>
                        <th>URL Lampiran</th>
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
                                @for ($i = 1; $i <= 3; $i++)
                                    @if($lesson["attachment_url$i"])
                                        <a href="{{ $lesson["attachment_url$i"] }}" target="_blank">Lampiran {{ $i }}</a><br>
                                    @endif
                                @endfor
                            </td>
                            <td>
                                <a href="{{ route('courses.admin.editLesson', [$course->id, $lesson->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                <a href="{{ route('courses.admin.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-info">Lihat</a>
                                <form action="{{ route('courses.admin.deleteLesson', [$course->id, $lesson->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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

    <!-- Projects -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- Form pencarian Projects -->
            <form method="GET" action="{{ route('admin.projects.index', $course->id) }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="project_search" class="form-control" placeholder="Cari project..." value="{{ request('project_search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <table class="table table-hover table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Nama Peserta</th>
                        <th>Judul Project</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($course->projects as $index => $project)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $project->user->name }}</td>
                            <td>{{ $project->judul }}</td>
                            <td>
                                <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada project</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
