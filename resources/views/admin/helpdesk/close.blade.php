@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h1>Apakah Anda yakin ingin menutup tiket ini?</h1>

    <form action="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" method="POST">
        @csrf
        @method('PUT')
        <button type="submit">Ya, tutup tiket</button>
        <a href="{{ route('admin.helpdesk.tickets.index') }}">Batal</a>
    </form>
</div>
@endsection
