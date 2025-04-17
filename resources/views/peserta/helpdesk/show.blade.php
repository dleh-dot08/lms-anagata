@extends('layouts.peserta.template')

@section('content')
<div class="container mt-5">
    <h3 class="mb-3">Detail Tiket: {{ $ticket->subject }}</h3>

    <div class="mb-4">
        <p><strong>Status:</strong> 
            <span class="badge 
                @if($ticket->status == 'open') badge-success 
                @elseif($ticket->status == 'closed') badge-danger 
                @else badge-secondary @endif">
                {{ ucfirst($ticket->status) }}
            </span>
        </p>
        <p><strong>Dibuat pada:</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>
    </div>

    <h5>Pesan:</h5>
    <div class="border p-3 mb-4" style="background-color: #f8f9fa">
        @foreach ($ticket->messages as $message)
            <div class="mb-3">
                <strong>{{ $message->user->name ?? $message->guest_name ?? 'Guest' }}:</strong> <br>
                <p class="mb-1">{{ $message->message }}</p>
                <small class="text-muted">{{ $message->created_at->format('d M Y H:i') }}</small>
                <hr>
            </div>
        @endforeach
    </div>

    @if ($ticket->status != 'closed')
    <form action="{{ route('peserta.helpdesk.message.store') }}" method="POST">
        @csrf
        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
        <div class="form-group">
            <label for="message">Balas Pesan:</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
    @else
        <div class="alert alert-warning mt-3">Tiket ini sudah ditutup dan tidak bisa dibalas.</div>
    @endif
</div>
@endsection
