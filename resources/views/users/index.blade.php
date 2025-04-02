@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Pengguna</h2>

        <!-- Form untuk filter dan pencarian -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
            <div class="row">
                <!-- Pencarian berdasarkan nama atau email -->
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email" value="{{ request('search') }}">
                </div>
                
                <!-- Filter berdasarkan role -->
                <div class="col-md-4">
                    <select name="role" class="form-control">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol filter dan pencarian -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>

        <!-- Tabel Daftar Pengguna -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop untuk menampilkan users berdasarkan role -->
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>
                                    <!-- Tombol aksi -->
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <!-- Tombol Hapus atau Restore berdasarkan status deleted_at -->
                                    @if($user->deleted_at)
                                        <form action="{{ route('users.restore', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                        </form>
                                    @else
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
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

                <!-- Pagination -->
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
