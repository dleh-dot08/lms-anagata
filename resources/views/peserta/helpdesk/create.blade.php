@extends('layouts.peserta.template')

@section('content')
<div class="container">
    <h2>Mulai Percakapan dengan Tim Helpdesk</h2>

    <form action="{{ route('peserta.helpdesk.store') }}" method="POST">
        @csrf
        <div>
            <label>Judul / Subjek Permasalahan</label>
            <input type="text" name="subject" required>
        </div>
        <div>
            <label>Pesan Pertama</label>
            <textarea name="message" rows="5" required></textarea>
        </div>
        <button type="submit">Kirim</button>
    </form>
</div>
@endsection
