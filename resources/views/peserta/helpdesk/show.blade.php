@extends('layouts.peserta.template')

@section('content')
<style>
    .chat-box {
        border: 1px solid #ccc;
        border-radius: 8px;
        height: 500px;
        overflow-y: auto;
        padding: 10px;
        background: #f9f9f9;
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
        align-self: flex-end;
    }

    .from-admin {
        background: #fff3e0;
        align-self: flex-start;
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
    <h3>Subjek: {{ $ticket->subject }}</h3>
    <div class="chat-box" id="chatBox">
        @foreach ($ticket->messages as $msg)
            <div class="chat-message 
                @if($msg->sender_type == 'user' || $msg->sender_type == 'guest') from-user
                @elseif($msg->sender_type == 'admin') from-admin
                @else from-system @endif">
                @if($msg->sender_type == 'admin')
                    <strong>Admin</strong>
                @elseif($msg->sender_type == 'user')
                    <strong>{{ $msg->user->name ?? 'Peserta' }}</strong>
                @elseif($msg->sender_type == 'guest')
                    <strong>{{ $ticket->guest_name ?? 'Tamu' }}</strong>
                @endif

                <div>{!! $msg->message !!}</div>
                <small>{{ $msg->created_at->format('d M Y H:i') }}</small>
            </div>
        @endforeach
    </div>

    {{-- Form kirim pesan lanjutan --}}
    <form action="{{ route('peserta.helpdesk.message.store', $ticket->id) }}" method="POST" class="mt-3">
        @csrf
        <div class="chat-input">
            <textarea name="message" class="form-control" rows="2" required placeholder="Tulis pesan balasan..."></textarea>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>
</div>
@endsection
