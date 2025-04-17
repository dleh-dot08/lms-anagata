@extends('layouts.admin.template')

@section('content')
<style>
    .chat-box {
        border: 1px solid #ccc;
        border-radius: 8px;
        height: 500px;
        overflow-y: auto;
        padding: 10px;
        background: #f3f4f6;
        display: flex;
        flex-direction: column;
    }

    .chat-message {
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 10px;
        max-width: 70%;
    }

    .from-user {
        background: #e0f7fa;
        align-self: flex-start;
    }

    .from-admin {
        background: #dcedc8;
        align-self: flex-end;
    }

    .from-system {
        background: #eeeeee;
        align-self: center;
        font-style: italic;
        font-size: 14px;
    }

    .chat-input {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<div class="container">
    <h3>Subjek Tiket: {{ $ticket->subject }}</h3>
    <p><strong>Status:</strong> {{ ucfirst($ticket->status) }} | <strong>Dibuat:</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>

    <div class="chat-box" id="chatBox">
        @foreach ($ticket->messages as $msg)
            <div class="chat-message 
                @if($msg->sender_type == 'user' || $msg->sender_type == 'guest') from-user
                @elseif($msg->sender_type == 'admin') from-admin
                @else from-system @endif">
                
                {{-- Nama Pengirim --}}
                @if($msg->sender_type == 'admin')
                    <strong>Admin</strong>
                @elseif($msg->sender_type == 'user')
                    <strong>{{ $msg->user->name ?? 'Peserta' }}</strong>
                @elseif($msg->sender_type == 'guest')
                    <strong>{{ $msg->guest_name ?? 'Tamu' }}</strong>
                @endif

                <div>{!! $msg->message !!}</div>
                <small>{{ $msg->created_at->format('d M Y H:i') }}</small>
            </div>
        @endforeach
    </div>

    {{-- Form kirim pesan admin --}}
    <form action="{{ route('admin.helpdesk.tickets.message.store', $ticket->id) }}" method="POST" class="mt-3">
        @csrf
        <div class="chat-input">
            <textarea name="message" class="form-control" rows="2" required placeholder="Tulis balasan admin..."></textarea>
            <button type="submit" class="btn btn-success">Kirim</button>
        </div>
    </form>

    @if($ticket->status !== 'closed')
        <form action="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" method="POST" class="mt-2">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-danger">Tutup Tiket</button>
        </form>
    @endif
</div>
@endsection
