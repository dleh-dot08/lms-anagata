@extends('layouts.sekolah.template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mentor Notes /</span> Courses
    </h4>

    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('sekolah.mentor-notes.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search course or mentor..." 
                               name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Course List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Mentor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($courses as $course)
                            <tr>
                                <td>{{ $course->nama_kelas }}</td>
                                <td>{{ $course->mentor->name ?? 'Not Assigned' }}</td>
                                <td>
                                    <a href="{{ route('sekolah.mentor-notes.meetings', $course->id) }}" class="btn btn-sm btn-info">
                                        <i class="bx bx-show-alt me-1"></i> View Meetings
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection