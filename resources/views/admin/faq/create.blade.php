@extends('layouts.admin.template')

@section('content')
    <h1>Create FAQ</h1>

    <form action="{{ route('admin.helpdesk.faq.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="question">Question</label>
            <input type="text" name="question" id="question" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="answer">Answer</label>
            <textarea name="answer" id="answer" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
@endsection
