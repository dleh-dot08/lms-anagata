@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Import Users dari CSV</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="csv_file">File CSV:</label>
                            <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="role_id">Peran Default:</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <h6>Format CSV yang diharapkan:</h6>
                            <p>File CSV harus memiliki kolom berikut:</p>
                            <ul>
                                <li>no (nomor urut)</li>
                                <li>nama (nama lengkap user)</li>
                                <li>email (alamat email user)</li>
                                <li>waktu verifikasi email (format: dd/mm/yyyy HH:ii:ss)</li>
                                <li>password (password yang akan digunakan)</li>
                            </ul>
                            <p class="mb-0">Contoh format:</p>
                            <pre class="mb-0">no,nama,email,waktu verifikasi email,password
1,John Doe,john@example.com,13/06/2025 15:50:57,rahasia123</pre>
                        </div>

                        <button type="submit" class="btn btn-primary">Import Users</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 