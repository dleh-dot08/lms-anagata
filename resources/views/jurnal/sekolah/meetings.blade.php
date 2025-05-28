@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mentor Notes /</span> Meetings
    </h4>

    <!-- Course Info -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $course->nama_kelas }}</h5>
            <p class="card-text">Mentor: {{ $course->mentor->name ?? 'Not Assigned' }}</p>
        </div>
    </div>

    <!-- Meetings List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Meeting</th>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Note Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($meetings as $meeting)
                            <tr>
                                <td>Pertemuan {{ $meeting->pertemuan }}</td>
                                <td>{{ $meeting->tanggal_pelaksanaan->format('d M Y') }}</td>
                                <td>{{ $meeting->judul ?? 'No Title' }}</td>
                                <td>
                                    @if($meeting->note)
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-danger">Not Created</span>
                                    @endif
                                </td>
                                <td>
                                    @if($meeting->note)
                                        <a href="{{ route('sekolah.mentor-notes.show', $meeting->id) }}" class="btn btn-sm btn-info">
                                            <i class="bx bx-show-alt me-1"></i> View Note
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="bx bx-x-circle me-1"></i> No Note
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No meetings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection