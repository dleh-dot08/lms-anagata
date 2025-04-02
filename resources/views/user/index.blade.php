@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Pengguna</h2>
            <a href="{{ route('user.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
        </div>

        <!-- Form Pencarian -->
        <div class="mb-4">
            <form action="{{ route('user.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="Cari pengguna...">
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select" onchange="this.form.submit()">
                            <option value="">Filter Berdasarkan Role</option>
                            <option value="1" {{ request()->role == 1 ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ request()->role == 2 ? 'selected' : '' }}>Mentor</option>
                            <option value="3" {{ request()->role == 3 ? 'selected' : '' }}>Peserta</option>
                            <option value="4" {{ request()->role == 4 ? 'selected' : '' }}>Karyawan</option>
                            <option value="5" {{ request()->role == 5 ? 'selected' : '' }}>Vendor</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Membagi Pengguna Berdasarkan Role -->
                @foreach($roles as $role)
                    <h4>{{ $role->name }} ({{ $role->users->count() }})</h4>
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($user->deleted_at)
                                            <del>{{ $user->name }}</del>
                                        @else
                                            {{ $user->name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->deleted_at)
                                            <del>{{ $user->email }}</del>
                                        @else
                                            {{ $user->email }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->deleted_at)
                                            <span class="badge bg-danger">Dihapus</span>
                                        @else
                                            <span class="badge bg-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                        @if(!$user->deleted_at)
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        @else
                                            <form action="{{ route('user.restore', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Paginasi untuk Role -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $role->users->links('pagination::bootstrap-4') }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
