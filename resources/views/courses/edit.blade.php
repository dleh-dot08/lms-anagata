@extends('layouts.admin.template')

@section('content')
<div class="container mt-4">
    <h2>Edit Kursus</h2>

    <form action="{{ route('courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('courses.form', ['course' => $course])
        <button type="submit" class="btn btn-primary">Perbarui</button>
    </form>
</div>
@endsection
