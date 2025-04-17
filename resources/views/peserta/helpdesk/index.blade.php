@extends('layouts.peserta.template')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Tiket Bantuan Saya</h2>
    <a href="{{ route('peserta.helpdesk.create') }}" class="btn btn-primary">Buat Pesan baru</a>

    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>Judul</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->subject }}</td>
                    <td>
                        <span class="badge 
                            @if($ticket->status == 'open') badge-success 
                            @elseif($ticket->status == 'closed') badge-danger 
                            @else badge-secondary @endif">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('peserta.helpdesk.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada tiket bantuan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
