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

            <!-- Form tambah peserta -->
            <form action="{{ route('courses.addParticipant', $course->id) }}" method="POST" class="row g-3 mb-4">
                @csrf
                <div class="col-md-6">
                    <label for="user_id" class="form-label">Pilih Peserta</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="">-- Pilih Peserta --</option>
                        @foreach($participants as $participant)
                            <option value="{{ $participant->id }}">{{ $participant->name }} ({{ $participant->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>

            <!-- Tabel daftar peserta -->
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
                                <form action="{{ route('courses.removeParticipant', ['id' => $course->id, 'participant_id' => $enrollment->user->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus peserta ini?')">
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
            <a href="#" class="btn btn-primary mb-3">Tambah Materi Pembelajaran</a>
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
                    
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection