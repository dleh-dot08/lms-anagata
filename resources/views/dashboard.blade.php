@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ Auth::user()->name }}!</p>

        @if(Auth::user()->role_as == 1)
            <h2>Dashboard Admin</h2>
            <p>Menu: Kelola Pengguna, Kelola Kelas, Laporan</p>

        @elseif(Auth::user()->role_as == 2)
            <h2>Dashboard Karyawan</h2>
            <p>Menu: Data Kursus, Jadwal, Absensi</p>

        @elseif(Auth::user()->role_as == 3)
            <h2>Dashboard Trainer</h2>
            <p>Menu: Kelola Materi, Kelola Siswa</p>

        @elseif(Auth::user()->role_as == 4)
            <h2>Dashboard Peserta</h2>
            <p>Menu: Kursus Saya, Sertifikat, Forum</p>

        @elseif(Auth::user()->role_as == 5)
            <h2>Dashboard Vendor</h2>
            <p>Menu: Kontrak, Pengelolaan Produk</p>

        @else
            <h2>Dashboard Umum</h2>
            <p>Anda tidak memiliki akses spesifik.</p>
        @endif
    </div>
@endsection
