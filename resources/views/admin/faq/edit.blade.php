@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h4>Edit FAQ</h4>

    <form action="{{ route('admin.helpdesk.faq.update', $faq->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="question">Pertanyaan</label>
            <input type="text" name="question" value="{{ $faq->question }}" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="answer">Jawaban</label>
            <textarea name="answer" id="ckeditor" class="form-control" rows="6" required>{!! $faq->answer !!}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="category">Kategori</label>
            <input type="text" name="category" value="{{ $faq->category }}" class="form-control">
        </div>

        <div class="form-group mb-4">
            <label for="is_active">Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $faq->is_active ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$faq->is_active ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.helpdesk.faq.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

{{-- CKEditor --}}
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script> CKEDITOR.replace('ckeditor'); </script>
@endsection
