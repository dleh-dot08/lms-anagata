@extends('layouts.peserta.template')

@section('content')
    <h2>Detail FAQ</h2>
    <p><strong>Pertanyaan:</strong> {{ $faq->question }}</p>
    <p><strong>Jawaban:</strong> {!! nl2br(e($faq->answer)) !!}</p>
    <!-- Tampilan FAQ untuk peserta -->
@endsection
