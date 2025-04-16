@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h1>Detail Tiket - {{ $ticket->subject }}</h1>

    <p><strong>Status:</strong> {{ $ticket->status }}</p>
    <p><strong>Dibuat pada:</strong> {{ $ticket->created_at }}</p>

    <h3>Pesan</h3>
    <div class="messages">
        @foreach($ticket->messages as $message)
            <div class="message">
                <p><strong>{{ $message->user->name }} ({{ $message->sender_type }})</strong></p>
                <p>{{ $message->message }}</p>
                <p><small>{{ $message->created_at }}</small></p>
            </div>
        @endforeach
    </div>

    <h3>Tambah Pesan</h3>
    <form action="{{ route('admin.helpdesk.tickets.message.store', $ticket->id) }}" method="POST">
        @csrf
        <textarea name="message" required></textarea>
        <button type="submit">Kirim Pesan</button>
    </form>

    @if($ticket->status !== 'closed')
        <form action="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit">Tutup Tiket</button>
        </form>
    @endif
</div>
@endsection
