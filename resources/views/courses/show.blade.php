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

            <p><strong>Mentor Utama:</strong> {{ $course->mentor->name ?? 'Tidak Ada' }}</p>
            <p><strong>Mentor Pengganti 1:</strong> {{ $course->mentor2->name ?? 'Tidak Ada' }}</p>
            <p><strong>Mentor Pengganti 2:</strong> {{ $course->mentor3->name ?? 'Tidak Ada' }}</p>

            <p><strong>Kategori:</strong> {{ $course->kategori->nama_kategori ?? 'Tidak Ada' }}</p>
            <p><strong>Jenjang:</strong> {{ $course->jenjang->nama_jenjang ?? 'Tidak Ada' }}</p>
            <p><strong>Kelas:</strong> {{ $course->kelas->nama ?? 'Tidak Ada' }}</p>
            <p><strong>Program:</strong> {{ $course->program->nama_program ?? 'Tidak Ada' }}</p>
            <p><strong>Sekolah:</strong> {{ $course->sekolah->nama_sekolah ?? 'Tidak Ada' }}</p>
            <p><strong>Level:</strong> {{ $course->level }}</p>
            <p><strong>Status:</strong> {{ $course->status }}</p>
            <p><strong>Deskripsi:</strong> {{ $course->deskripsi ?? 'Tidak Ada' }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $course->waktu_mulai }}</p>
            <p><strong>Waktu Akhir:</strong> {{ $course->waktu_akhir }}</p>
            <p><strong>Harga:</strong> {{ $course->harga ?? 'Gratis' }}</p>
            <p><strong>Jumlah Peserta:</strong> {{ $course->jumlah_peserta }}</p>
        </div>
    </div>

    {{-- Bagian Card Daftar Peserta --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Daftar Peserta</h4>
                <a href="{{ route('courses.formparticipant', $course->id) }}" class="btn btn-success">
                    + Tambah Peserta
                </a>
            </div>

            <form method="GET" action="{{ route('courses.show', $course->id) }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari peserta..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
            </form>

            <div class="table-responsive"> 
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Peserta</th>
                            <th>Jenjang</th>
                            <th>Sekolah</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Persentase Kehadiran</th>
                            <th style="min-width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $index => $enrollment)
                            <tr>
                                <td>{{ ($enrollments->currentPage() - 1) * $enrollments->perPage() + $loop->iteration }}</td>
                                <td>{{ $enrollment->user->name ?? '-' }}</td>
                                <td>{{ $enrollment->jenjang->nama_jenjang ?? '-' }}</td>
                                <td>{{ $enrollment->sekolah->nama_sekolah ?? '-' }}</td>
                                <td>{{ $enrollment->kelas->nama ?? '-' }}</td>
                                <td>
                                    @if ($enrollment->status === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif ($enrollment->status === 'tidak_aktif')
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @else
                                        {{-- Fallback untuk status yang tidak dikenal --}}
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $enrollment->status)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalLessons = \App\Models\Lesson::where('course_id', $course->id)->count();
                                        $presentCount = \App\Models\Attendance::where('course_id', $course->id)
                                            ->where('user_id', $enrollment->user->id)
                                            ->whereIn('status', ['Hadir', 'Izin'])
                                            ->count();
                                        $attendancePercentage = $totalLessons > 0 ? ($presentCount / $totalLessons) * 100 : 0;
                                    @endphp
                                    {{ number_format($attendancePercentage, 2) }}%
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editStatusModal-{{ $enrollment->id }}">
                                            Edit
                                        </button>
                                        
                                        <form action="{{ route('courses.participants.destroy', ['course' => $course->id, 'user' => $enrollment->user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada peserta</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> 

            <div class="d-flex justify-content-center mt-4">
                {{ $enrollments->withQueryString()->onEachSide(1)->links('pagination.custom') }}
            </div>
        </div>
    </div>

    @foreach($enrollments as $enrollment)
    <div class="modal fade" id="editStatusModal-{{ $enrollment->id }}" tabindex="-1" aria-labelledby="editStatusModalLabel-{{ $enrollment->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStatusModalLabel-{{ $enrollment->id }}">Edit Status Peserta: {{ $enrollment->user->name ?? 'N/A' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('courses.participants.updateStatus', ['course' => $course->id, 'user' => $enrollment->user->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status-{{ $enrollment->id }}" class="form-label">Status</label>
                            <select class="form-select" id="status-{{ $enrollment->id }}" name="status" required>
                                <option value="aktif" @if($enrollment->status === 'aktif') selected @endif>Aktif</option>
                                <option value="tidak_aktif" @if($enrollment->status === 'tidak_aktif') selected @endif>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Bagian Card Daftar Pertemuan --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Daftar Pertemuan</h4>
                <a href="{{ route('meetings.create', $course->id) }}" class="btn btn-success">
                    + Tambah Pertemuan
                </a>
            </div>

            <div class="table-responsive"> {{-- Wrapper untuk responsivitas tabel --}}
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Pertemuan Ke</th>
                            <th>Judul</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th style="min-width: 150px;">Aksi</th> {{-- Beri min-width agar tombol tidak terlalu sempit --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($course->meetings as $index => $meeting)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $meeting->pertemuan }}</td>
                                <td>{{ $meeting->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d M Y') }}</td>
                                <td>{{ $meeting->jam_mulai ? \Carbon\Carbon::parse($meeting->jam_mulai)->format('H:i') : '-' }}</td>
                                <td>{{ $meeting->jam_selesai ? \Carbon\Carbon::parse($meeting->jam_selesai)->format('H:i') : '-' }}</td>
                                <td>
                                    <div class="d-flex gap-1"> {{-- Mengelompokkan tombol aksi --}}
                                        <a href="{{ route('meetings.edit', [$course->id, $meeting->id]) }}" class="btn btn-warning btn-sm">Edit</a>
                                        
                                        @if(!empty($meeting->schedule_history))
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#historyModal-{{ $meeting->id }}">
                                                Riwayat
                                            </button>
                                        @endif

                                        <form action="{{ route('meetings.destroy', [$course->id, $meeting->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pertemuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada pertemuan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modals untuk setiap riwayat perubahan pertemuan --}}
    {{-- Tempatkan ini di bagian paling bawah file show.blade.php Anda, misalnya sebelum @endsection --}}
    @foreach($course->meetings as $meeting)
    @if(!empty($meeting->schedule_history))
    <div class="modal fade" id="historyModal-{{ $meeting->id }}" tabindex="-1" aria-labelledby="historyModalLabel-{{ $meeting->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel-{{ $meeting->id }}">Riwayat Perubahan Jadwal Pertemuan {{ $meeting->pertemuan }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(count($meeting->schedule_history) > 0)
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Waktu Perubahan</th>
                                    <th>Diubah Oleh</th>
                                    <th>Jadwal Lama</th>
                                    <th>Jadwal Baru</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Tampilkan dari yang terbaru ke terlama --}}
                                @foreach(array_reverse($meeting->schedule_history) as $history) 
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($history['changed_at'])->format('d M Y H:i') }}</td>
                                    <td>{{ $history['changed_by_user_name'] ?? 'N/A' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($history['old_tanggal_pelaksanaan'])->format('d/m/Y') }}<br>
                                        {{ $history['old_jam_mulai'] }} - {{ $history['old_jam_selesai'] }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($history['new_tanggal_pelaksanaan'])->format('d/m/Y') }}<br>
                                        {{ $history['new_jam_mulai'] }} - {{ $history['new_jam_selesai'] }}
                                    </td>
                                    <td>{{ $history['reason'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Tidak ada riwayat perubahan jadwal untuk pertemuan ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach    {{-- Silabus --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Upload Silabus PDF</h5>
            <form action="{{ route('courses.uploadSilabus', $course->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="silabus_pdf">Link Google Drive (format: drive.google.com/.../view)</label>
                    <input type="url" name="silabus_pdf" class="form-control" placeholder="https://drive.google.com/file/d/..." required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Link Silabus</button>
            </form>

            {{-- Tampilkan link silabus jika ada --}}
            @if ($course->silabus_pdf)
                @php
                    $previewSilabus = convertToPreview($course->silabus_pdf);
                @endphp
                <hr>
                <a href="{{ $previewSilabus }}" target="_blank" class="btn btn-sm btn-dark mt-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Silabus (Preview)
                </a>
            @endif
        </div>
    </div>

    {{-- RPP --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Upload RPP (Link Google Drive)</h5>
            <form action="{{ route('courses.uploadRpp', $course->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="rpp_drive_link">Link Google Drive (format: drive.google.com/.../view)</label>
                    <input type="url" name="rpp_drive_link" class="form-control" placeholder="https://drive.google.com/file/d/..." required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Link RPP</button>
            </form>

            {{-- Tampilkan link RPP jika ada --}}
            @if ($course->rpp_drive_link)
                @php
                    $previewRpp = convertToPreview($course->rpp_drive_link);
                @endphp
                <hr>
                <a href="{{ $previewRpp }}" target="_blank" class="btn btn-sm btn-dark mt-2">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Lihat RPP (Preview)
                </a>
            @endif
        </div>
    </div>

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
                            <td>Pertemuan {{ $lesson->meeting->pertemuan ?? '-' }}</td>
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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('courses.show', $course->id) }}" class="mb-3">
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
                            <td>{{ $project->title }}</td>
                            <td>
                                <a href="{{ route('projects.admin.show', $project->id) }}" class="btn btn-sm btn-info">Detail</a>
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