@extends('layouts.admin.template')

@section('content')
<div class="container">
    <h1>Daftar Tiket Helpdesk</h1>

    <form action="{{ route('admin.helpdesk.tickets.index') }}" method="GET">
        <input type="text" name="search" placeholder="Search tiket" value="{{ request('search') }}">
        <button type="submit">Cari</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->subject }}</td>
                    <td>{{ $ticket->status }}</td>
                    <td>{{ $ticket->created_at }}</td>
                    <td>
                        <a href="{{ route('admin.helpdesk.tickets.show', $ticket->id) }}">Detail</a>
                        @if($ticket->status !== 'closed')
                            <a href="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" onclick="return confirm('Are you sure?')">Tutup</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $tickets->links() }}
</div>
@endsection
