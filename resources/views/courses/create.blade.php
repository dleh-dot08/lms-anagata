@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Buat Kursus Baru</h2>

    <form action="{{ route('courses.store') }}" method="POST">
        @csrf
        @include('courses.form')
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
