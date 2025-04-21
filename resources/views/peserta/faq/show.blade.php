@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h4>Detail FAQ</h4>

    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $faq->question }}</h5>
            <hr>
            <p><strong>Kategori:</strong> {{ $faq->category ?? '-' }}</p>
            <p><strong>Status:</strong> {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}</p>
            <p><strong>Dibuat:</strong> {{ $faq->created_at->format('d M Y H:i') }}</p>
            <hr>
            <div>{!! $faq->answer !!}</div>
        </div>
    </div>

    <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
