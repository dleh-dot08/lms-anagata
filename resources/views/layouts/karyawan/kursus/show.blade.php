@extends('layouts.karyawan.template')

@section('content')
<div class="container mt-4">
    <h2>Detail Kursus</h2>

    {{-- Info Ringkas Kursus --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3>{{ $course->nama_kelas }}</h3>
            <p><strong>Kode Unik:</strong> {{ $course->kode_unik }}</p>
            <p><strong>Mentor:</strong> {{ $course->mentor->name }}</p>
            <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori }}</p>
            <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang }}</p>
            <p><strong>Program:</strong> {{ $course->program->nama_program ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $course->level }}</p>
            <p><strong>Status:</strong> {{ $course->status }}</p>
            <p><strong>Waktu:</strong> 
               {{ $course->waktu_mulai ? $course->waktu_mulai->format('d M Y') : '-' }} – 
               {{ $course->waktu_akhir ? $course->waktu_akhir->format('d M Y') : '-' }}
            </p>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
              <h4>Daftar Peserta</h4>
              <a href="{{ route('courses.apd.formparticipant', $course->id) }}" class="btn btn-success">
                  + Tambah Peserta
              </a>
          </div>

          <!-- Form pencarian -->
          <form method="GET" action="{{ route('courses.apd.show', $course->id) }}" class="mb-3">
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
                              <form action="{{ route('courses.apd.participants.destroy', ['course' => $course->id, 'user' => $enrollment->user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
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
          <div class="d-flex justify-content-center mt-4">
            {{ $enrollments->links() }}
          </div>
      </div>
  </div>

    <!-- Materi -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
          <a href="{{ route('courses.apd.createLessonForm', $course->id) }}" class="btn btn-primary mb-3">Tambah Materi Pembelajaran</a>
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
                              <a href="{{ route('courses.apd.editLesson', [$course->id, $lesson->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                              <a href="{{ route('courses.apd.showLesson', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-info">Lihat</a>
                              <form action="{{ route('courses.apd.deleteLesson', [$course->id, $lesson->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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

    {{-- Kembali ke daftar kursus APD --}}
    <a href="{{ route('courses.apd.index') }}" class="btn btn-secondary">
      <i class="bi bi-arrow-left-circle me-1"></i> Kembali
    </a>
</div>
@endsection
