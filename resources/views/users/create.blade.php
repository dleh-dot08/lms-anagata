@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <h2>Tambah Pengguna</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label>Nama:</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="form-group mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="form-group mb-3">
            <div class="form-check">
                <input type="checkbox" name="verify_email" class="form-check-input" id="verifyEmail" value="1" checked>
                <label class="form-check-label" for="verifyEmail">Verifikasi email langsung</label>
            </div>
        </div>
        <div class="form-group mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label>Konfirmasi Password:</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label>Peran:</label>
            <select name="role_id" class="form-control" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3">
            <label>Foto:</label>
            <input type="file" name="foto_diri" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

</div>
@endsection
