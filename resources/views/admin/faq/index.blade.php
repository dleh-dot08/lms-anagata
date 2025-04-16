@extends('layouts.admin.template')

@section('content')
    <h1>FAQ Management</h1>
    <a href="{{ route('admin.helpdesk.faq.create') }}" class="btn btn-primary">Create New FAQ</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Question</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faqs as $faq)
                <tr>
                    <td>{{ $faq->question }}</td>
                    <td>
                        <a href="{{ route('admin.helpdesk.faq.edit', $faq->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.helpdesk.faq.destroy', $faq->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
