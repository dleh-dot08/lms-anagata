@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-people-fill text-primary me-2"></i>Daftar Pengguna
        </h2>
        <button type="button" class="btn btn-primary d-flex align-items-center" onclick="window.location.href='{{ route('users.create') }}'">
            <i class="bi bi-person-plus-fill me-2"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Card untuk filter dan pencarian -->
    <div class="card shadow-sm mb-4 border-0 rounded-3">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row g-3 align-items-end">
                    <!-- Pencarian berdasarkan nama atau email -->
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-search me-1"></i>Cari Pengguna
                        </label>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control" 
                            placeholder="Nama atau email pengguna" 
                            value="{{ request('search') }}"
                        >
                    </div>
                    
                    <!-- Filter berdasarkan role -->
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-filter me-1"></i>Filter Role
                        </label>
                        <select name="role" class="form-select">
                            <option value="">Semua Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter berdasarkan status -->
                    <div class="col-lg-2 col-md-2 col-sm-12">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-toggle-on me-1"></i>Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Tombol filter dan reset -->
                    <div class="col-lg-2 col-md-2 col-sm-12">
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Menampilkan jumlah hasil pencarian -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">
            Menampilkan <strong>{{ $users->count() }}</strong> dari <strong>{{ $users->total() }}</strong> pengguna
        </span>
        
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cardsView">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="tableView">
                <i class="bi bi-table"></i>
            </button>
        </div>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="card shadow-sm border-0 rounded-3" id="tableViewContent">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 900px;">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">#</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($users->count() > 0)
                            @foreach($users as $user)
                                <tr class="{{ $user->deleted_at ? 'table-light' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-{{ $user->deleted_at ? 'secondary' : 'primary' }} me-3 rounded-circle text-white d-flex align-items-center justify-content-center">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <span class="{{ $user->deleted_at ? 'text-decoration-line-through text-muted' : 'fw-medium' }}">
                                                {{ $user->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ getRoleBadgeColor($user->role->name) }} text-white">
                                            {{ $user->role->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->deleted_at)
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @else
                                            <span class="badge bg-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-info" data-bs-toggle="tooltip" title="Detail">
                                                <i class="bi bi-eye-fill me-1"></i> Detail
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="bi bi-pencil-fill me-1"></i> Edit
                                            </a>

                                            <!-- Tombol Hapus atau Restore berdasarkan status deleted_at -->
                                            @if($user->deleted_at)
                                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" data-bs-toggle="tooltip" title="Aktifkan">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Aktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}" data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="bi bi-trash-fill me-1"></i> Hapus
                                                </button>

                                                <!-- Modal Konfirmasi Hapus -->
                                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Non-aktifkan</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Anda yakin ingin menonaktifkan pengguna <strong>{{ $user->name }}</strong>?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Batal</button>
                                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-lg">Non-aktifkan</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center py-5">
                                        <i class="bi bi-search text-muted" style="font-size: 2rem;"></i>
                                        <h5 class="mt-3">Tidak ada data pengguna ditemukan</h5>
                                        <p class="text-muted">Coba ubah filter pencarian Anda</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light">
                    <div class="text-muted small">
                        Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
                    </div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Card View (Tersembunyi secara default) -->
    <div class="row g-3" id="cardsViewContent" style="display: none;">
        @if($users->count() > 0)
            @foreach($users as $user)
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                    <div class="card h-100 border-0 shadow-sm {{ $user->deleted_at ? 'bg-light' : '' }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="avatar bg-{{ $user->deleted_at ? 'secondary' : 'primary' }} rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 20px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="badge bg-{{ getRoleBadgeColor($user->role->name) }} text-white">
                                    {{ $user->role->name }}
                                </span>
                            </div>
                            <h5 class="card-title {{ $user->deleted_at ? 'text-decoration-line-through text-muted' : '' }}">{{ $user->name }}</h5>
                            <p class="card-text text-muted mb-1">
                                <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                            </p>
                            <p class="card-text mb-3">
                                @if($user->deleted_at)
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </p>
                            <div class="d-flex gap-1 mt-3">
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-info flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                @if($user->deleted_at)
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Aktifkan
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#deleteModalCard{{ $user->id }}">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>

                                    <!-- Modal Konfirmasi Hapus (Card View) -->
                                    <div class="modal fade" id="deleteModalCard{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Konfirmasi Non-aktifkan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Anda yakin ingin menonaktifkan pengguna <strong>{{ $user->name }}</strong>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-lg">Non-aktifkan</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 2rem;"></i>
                        <h5 class="mt-3">Tidak ada data pengguna ditemukan</h5>
                        <p class="text-muted">Coba ubah filter pencarian Anda</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination for Card View -->
    @if($users->count() > 0)
        <div class="card mt-3 border-0 shadow-sm d-none" id="cardsPagination">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
                </div>
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Script untuk toggle view (table vs cards)
    document.addEventListener('DOMContentLoaded', function() {
        const tableView = document.getElementById('tableView');
        const cardsView = document.getElementById('cardsView');
        const tableViewContent = document.getElementById('tableViewContent');
        const cardsViewContent = document.getElementById('cardsViewContent');
        const cardsPagination = document.getElementById('cardsPagination');

        tableView.addEventListener('click', function() {
            tableView.classList.add('active');
            cardsView.classList.remove('active');
            tableViewContent.style.display = 'block';
            cardsViewContent.style.display = 'none';
            if (cardsPagination) cardsPagination.classList.add('d-none');
        });

        cardsView.addEventListener('click', function() {
            cardsView.classList.add('active');
            tableView.classList.remove('active');
            cardsViewContent.style.display = 'flex';
            tableViewContent.style.display = 'none';
            if (cardsPagination) cardsPagination.classList.remove('d-none');
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush

@php
// Helper function untuk warna badge berdasarkan role
function getRoleBadgeColor($roleName) {
    switch(strtolower($roleName)) {
        case 'admin':
            return 'danger';
        case 'pengajar':
            return 'warning';
        case 'peserta':
            return 'info';
        default:
            return 'secondary';
    }
}
@endphp
@endsection