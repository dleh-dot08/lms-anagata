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
    .chat-input {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<div class="container">
    <h2>Buat Tiket Helpdesk</h2>

    <form action="{{ route('peserta.helpdesk.store') }}" method="POST">
        @csrf

        @if (!auth()->check())
            <input type="text" name="guest_name" placeholder="Nama Lengkap" class="form-control mb-2" required>
            <input type="email" name="guest_email" placeholder="Email" class="form-control mb-2" required>
            <input type="text" name="guest_phone" placeholder="Nomor HP (opsional)" class="form-control mb-2">
        @endif

        <input type="text" name="subject" placeholder="Subjek Masalah" class="form-control mb-3" required>

        <div class="chat-box" id="chatBox">
            {{-- Chat kosong karena ini ticket baru --}}
            <div class="chat-message from-user">
                Silakan jelaskan masalah atau pertanyaan Anda.
            </div>
        </div>

        <div class="chat-input">
            <textarea name="message" class="form-control" placeholder="Tulis pesan pertama..." required></textarea>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>
</div>
@endsection
