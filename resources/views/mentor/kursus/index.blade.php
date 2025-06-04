@extends('layouts.mentor.template')

@section('content')
<div class="container py-4"> {{-- Tambah padding vertikal untuk tampilan yang lebih baik --}}
    <h3 class="fw-bold py-2 mb-3 text-primary text-center text-md-start">Daftar Kursus Anda</h3> {{-- Judul lebih menonjol dan responsif --}}

    <div class="card shadow-sm p-lg-4 p-3"> {{-- Sesuaikan padding untuk layar besar/kecil --}}

        {{-- Pencarian dan Filter --}}
        <form method="GET" action="{{ route('mentor.kursus.index') }}" class="mb-4"> {{-- Tambah margin-bottom --}}
            <div class="row g-3"> {{-- Gunakan g-3 untuk gap antar elemen form --}}
                <div class="col-md-5 col-lg-6"> {{-- Lebar kolom untuk input pencarian --}}
                    <div class="input-group">
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari kursus berdasarkan nama...">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3"> {{-- Lebar kolom untuk select filter --}}
                    <select name="status" class="form-select"> {{-- Gunakan form-select untuk styling Bootstrap 5 --}}
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request()->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ request()->status == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-3"> {{-- Lebar kolom untuk tombol filter --}}
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabel Kursus --}}
        <div class="table-responsive"> {{-- Penting untuk tabel responsif --}}
            <table class="table table-hover table-striped align-middle"> {{-- Tambah hover dan striped untuk estetika, align-middle untuk vertikal --}}
                <thead class="table-primary"> {{-- Header tabel berwarna --}}
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">Nama Kursus</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Mentor</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course) {{-- Gunakan @forelse untuk handle data kosong --}}
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td><span class="fw-semibold">{{ $course->nama_kelas }}</span></td>
                        <td>{{ $course->kategori->nama_kategori ?? 'N/A' }}</td>
                        <td>
                            <div class="small text-muted">Utama: <span class="fw-medium text-dark">{{ $course->mentor->name ?? 'N/A' }}</span></div>
                            @if($course->mentor2)
                                <div class="small text-muted">Cadangan 1: <span class="fw-normal text-dark">{{ $course->mentor2->name }}</span></div>
                            @endif
                            @if($course->mentor3)
                                <div class="small text-muted">Cadangan 2: <span class="fw-normal text-dark">{{ $course->mentor3->name }}</span></div>
                            @endif
                        </td>
                        <td>
                            @if ($course->status_dinamis == 'Aktif')
                                <span class="badge bg-success py-2 px-3 rounded-pill">Aktif</span>
                            @else
                                <span class="badge bg-danger py-2 px-3 rounded-pill">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($course->status == 'Aktif') {{-- Menggunakan status yang disimpan di DB --}}
                                <a href="{{ route('mentor.kursus.show', $course->id) }}" class="btn btn-info btn-sm rounded-pill shadow-sm">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            @else
                                <span class="text-muted small">Terkunci</span>
                                <button class="btn btn-secondary btn-sm rounded-pill shadow-sm" disabled>
                                    <i class="bi bi-lock me-1"></i> Detail
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                            Tidak ada kursus yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->onEachSide(1)->links('pagination.custom') }}
        </div>
    </div>
</div>
@endsection