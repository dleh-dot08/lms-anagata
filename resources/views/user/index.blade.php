@extends('layouts.admin.template')

@section('content')
    <div class="container mt-4">
        <h2>Daftar Pengguna</h2>
        <div class="accordion" id="userAccordion">
            <!-- Admin -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAdmin">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin" aria-expanded="true" aria-controls="collapseAdmin">
                        Admin
                    </button>
                </h2>
                <div id="collapseAdmin" class="accordion-collapse collapse show" aria-labelledby="headingAdmin" data-bs-parent="#userAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mentor -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingMentor">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMentor" aria-expanded="true" aria-controls="collapseMentor">
                        Mentor
                    </button>
                </h2>
                <div id="collapseMentor" class="accordion-collapse collapse" aria-labelledby="headingMentor" data-bs-parent="#userAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mentors as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Peserta -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingPeserta">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePeserta" aria-expanded="true" aria-controls="collapsePeserta">
                        Peserta
                    </button>
                </h2>
                <div id="collapsePeserta" class="accordion-collapse collapse" aria-labelledby="headingPeserta" data-bs-parent="#userAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Karyawan -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingKaryawan">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKaryawan" aria-expanded="true" aria-controls="collapseKaryawan">
                        Karyawan
                    </button>
                </h2>
                <div id="collapseKaryawan" class="accordion-collapse collapse" aria-labelledby="headingKaryawan" data-bs-parent="#userAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Vendor -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingVendor">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVendor" aria-expanded="true" aria-controls="collapseVendor">
                        Vendor
                    </button>
                </h2>
                <div id="collapseVendor" class="accordion-collapse collapse" aria-labelledby="headingVendor" data-bs-parent="#userAccordion">
                    <div class="accordion-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vendors as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $user->id) }}" class="btn btn-info btn-sm">Detail</a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
