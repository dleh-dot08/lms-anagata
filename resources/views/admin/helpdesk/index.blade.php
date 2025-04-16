@extends('layouts.admin.template')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Tiket Helpdesk</h1>

    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('admin.helpdesk.tickets.index') }}" method="GET" class="form-inline">
                <input type="text" name="search" class="form-control" placeholder="Cari tiket..." value="{{ request('search') }}" style="width: 80%">
                <button type="submit" class="btn btn-primary ml-2">Cari</button>
            </form>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('admin.helpdesk.tickets.create') }}" class="btn btn-success">Buat Tiket Baru</a>
        </div>
    </div>

    <!-- Daftar Tiket -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
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
                        <td>
                            <span class="badge 
                                @if($ticket->status == 'open') 
                                    badge-success
                                @elseif($ticket->status == 'closed') 
                                    badge-danger
                                @else 
                                    badge-warning 
                                @endif">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                        <td>{{ $ticket->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.helpdesk.tickets.show', $ticket->id) }}" class="btn btn-info btn-sm">Detail</a>
                            @if($ticket->status !== 'closed')
                                <a href="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to close this ticket?')">Tutup</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
