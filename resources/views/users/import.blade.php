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
                            <ul class="mb-2">
                                <li><b>nama</b></li>
                                <li><b>email</b></li>
                                <li><b>password</b></li>
                                <li><b>jenjang_id</b> (ambil dari tabel referensi di bawah)</li>
                                <li><b>kelas_id</b> (ambil dari tabel referensi di bawah)</li>
                            </ul>

                            <p class="mb-0">Contoh:</p>
                    <pre class="mb-0">nama,email,password,jenjang_id,kelas_id
                    Budi,budi@student.ruanganata.id,123456,1,1
                    Siti,siti@student.ruanganata.id,123456,3,8</pre>

                            <hr class="my-2">
                            <small class="text-muted">
                                Saat import, sistem otomatis mengisi email_verified_at = now(), verified_by_admin_at = now(), dan verified_by_admin_id = admin yang import.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">Import Users</button>
                    </form>

                    <div class="card mt-3">
                        <div class="card-header">
                            <strong>Referensi Jenjang dan Kelas (Realtime)</strong>
                            <div class="small text-muted">Gunakan id yang ada di database yaitu (kolom id_jenjang dan id_kelas).</div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width:80px;">id Kelas</th>
                                        <th>Nama</th>
                                        <th style="width:120px;">id Jenjang</th>
                                        <th> jenjang </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kelas as $k)
                                        <tr>
                                            <td>{{ $k->id }}</td>
                                            <td>{{ $k->nama }}</td>
                                            <td>{{ $k->id_jenjang }}</td>
                                            <td>{{ $jenjangs->where('id', $k->id_jenjang)->first()->nama_jenjang ?? 'Tidak Diketahui' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 