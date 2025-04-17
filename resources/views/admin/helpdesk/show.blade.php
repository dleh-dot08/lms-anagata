@extends('layouts.admin.template')

@section('content')
<div class="container mx-auto max-w-3xl py-6">
    <h1 class="text-2xl font-bold mb-2">Detail Tiket: {{ $ticket->subject }}</h1>
    <p class="text-gray-600 mb-4"><strong>Status:</strong> {{ ucfirst($ticket->status) }} | <strong>Dibuat pada:</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>

    <div class="bg-white shadow rounded-lg p-4 space-y-4 mb-6">
        @foreach($ticket->messages as $message)
            <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-md px-4 py-2 rounded-lg {{ 
                    $message->sender_type === 'admin' ? 'bg-blue-100 text-right' : 
                    ($message->sender_type === 'system' ? 'bg-gray-200 text-gray-800' : 'bg-green-100') 
                }}">
                    <p class="text-sm font-semibold mb-1">{{ $message->sender_name }}</p>
                    <p class="text-base whitespace-pre-line">{{ $message->message }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $message->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <h3 class="text-lg font-semibold mb-2">Balas Tiket</h3>
    <form action="{{ route('admin.helpdesk.tickets.message.store', $ticket->id) }}" method="POST" class="space-y-4 mb-4">
        @csrf
        <textarea name="message" rows="4" class="w-full border border-gray-300 rounded-lg p-2" required></textarea>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Kirim Pesan</button>
    </form>

    @if($ticket->status !== 'closed')
        <form action="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Tutup Tiket</button>
        </form>
    @endif
</div>
@endsection
