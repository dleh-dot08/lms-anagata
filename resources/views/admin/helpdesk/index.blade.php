@extends('layouts.admin.template')

@section('content')
<style>
    .pagination .page-link {
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
</style>
<div class="container mt-5">
    <h1 class="mb-4">Daftar Tiket Helpdesk</h1>

    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('admin.helpdesk.tickets.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari tiket..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
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
                            <span class="badge px-3 py-1 fw-bold 
                                @if($ticket->status == 'open') 
                                    bg-primary
                                @elseif($ticket->status == 'closed') 
                                    bg-danger
                                @else 
                                    bg-warning text-dark
                                @endif">
                                {{ strtoupper($ticket->status) }}
                            </span>

                        </td>
                        <td>{{ $ticket->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.helpdesk.tickets.show', $ticket->id) }}" class="btn btn-info btn-sm">Detail</a>
                            @if($ticket->status !== 'closed')
                            <form action="{{ route('admin.helpdesk.tickets.close', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to close this ticket?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger btn-sm">Tutup</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->onEachSide(1)->links('pagination.custom') }}
    </div>
</div>
@endsection
