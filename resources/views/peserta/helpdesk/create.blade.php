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
        white-space: pre-line;
    }
    .from-user {
        background: #e0f7fa;
        align-self: flex-end;
    }
    .from-admin {
        background: #fff3e0;
        align-self: flex-start;
    }
    .from-guest {
        background: #e0f7fa;
        align-self: flex-end;
    }
    .chat-input {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<div class="container">
    <h2>{{ isset($ticket) ? 'Detail Tiket Helpdesk' : 'Buat Tiket Helpdesk' }}</h2>

    @if (!isset($ticket))
        {{-- ✏️ Form to Create New Ticket --}}
        <form action="{{ route('peserta.helpdesk.store') }}" method="POST">
            @csrf

            @if (!auth()->check())
                <input type="text" name="guest_name" placeholder="Nama Lengkap" class="form-control mb-2" required>
                <input type="email" name="guest_email" placeholder="Email" class="form-control mb-2" required>
                <input type="text" name="guest_phone" placeholder="Nomor HP (opsional)" class="form-control mb-2">
            @endif

            <input type="text" name="subject" placeholder="Subjek Masalah" class="form-control mb-3" required>

            <div class="chat-box" id="chatBox">
                <div class="chat-message from-user">
                    Silakan jelaskan masalah atau pertanyaan Anda.
                </div>
            </div>

            <div class="chat-input">
                <textarea name="message" class="form-control" placeholder="Tulis pesan pertama..." required></textarea>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    @else
        {{-- ✅ After Ticket Creation --}}
        <p class="text-muted mb-3"><strong>Subjek:</strong> {{ $ticket->subject }}</p>

        <div class="chat-box" id="chatBox">
            @forelse ($ticket->messages as $msg)
            <div class="chat-message 
                {{ $msg->sender_type === 'admin' ? 'from-admin' : 
                ($msg->sender_type === 'guest' ? 'from-guest' : 
                ($msg->sender_type === 'system' ? 'from-admin' : 'from-user')) }}">
                {!! nl2br(e($msg->message)) !!}
            </div>
            @empty
                <div class="chat-message from-user">
                    Belum ada pesan.
                </div>
            @endforelse
        </div>

        {{-- You can hide the form or allow reply --}}
        <form action="{{ route('peserta.helpdesk.message.store', ['id' => $ticket->id]) }}" method="POST">
            @csrf
            <div class="chat-input">
                <textarea name="message" class="form-control" placeholder="Tulis pesan balasan..." required></textarea>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    @endif
</div>
@endsection
