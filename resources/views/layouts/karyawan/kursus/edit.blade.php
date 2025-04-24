@extends('layouts.karyawan.template')

@section('content')
<div class="container mt-4">
    <h2>Edit Kursus</h2>

    <form action="{{ route('courses.apd.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('layouts.karyawan.kursus.form', ['course' => $course])
        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection
