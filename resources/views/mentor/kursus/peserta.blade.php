@extends('layouts.mentor.template')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">{{ $course->nama_kelas }}</h3>
    @include('mentor.kursus.partials.nav-tabs', ['activeTab' => 'peserta'])
    <div class="card shadow border-0 rounded-lg">
        <!-- Header with course information -->
        <div class="card-header bg-primary text-white p-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 text-white">Daftar Peserta</h4>
                <h6 class="mb-0 text-white">{{ $course->nama_kelas }}</h6>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-primary fs-6 me-2">
                    {{ $course->participants->count() }} Peserta
                </span>
                {{-- <button class="btn btn-sm btn-light" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak
                </button> --}}
            </div>
        </div>

        <!-- Card body with search and participant list -->
        <div class="card-body p-4">
            <!-- Search and filter section -->
            {{-- <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="participantSearch" class="form-control" placeholder="Cari peserta...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    @if($course->participants->count() > 0)
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-file-export me-1"></i> Export
                        </button>
                    @endif
                </div>
            </div> --}}

            <!-- Participant list section -->
            <div class="participant-list-container">
                @if($course->participants->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="participantsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="70px">No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th class="text-center" width="120px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->participants as $index => $participant)
                                    <tr class="participant-row">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px">
                                                    {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $participant->name }}</h6>
                                                    <small class="text-muted d-md-none">{{ $participant->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell">{{ $participant->email }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <img src="{{ asset('images/empty-state.svg') }}" alt="No participants" class="img-fluid mb-3" style="max-width: 200px;">
                        <h5 class="text-muted">Belum ada peserta yang mendaftar</h5>
                        <p class="text-muted">Bagikan link kursus ini untuk mengundang peserta</p>
                        <button class="btn btn-primary mt-2">
                            <i class="fas fa-share-alt me-2"></i>Bagikan Kursus
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card footer with pagination if needed -->
        @if($course->participants->count() > 10)
        <div class="card-footer bg-white border-top p-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left"></i></a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pilih format untuk mengekspor data peserta:</p>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-outline-secondary flex-grow-1 py-3">
                        <i class="fas fa-file-excel me-2 text-success"></i>Excel
                    </button>
                    <button class="btn btn-outline-secondary flex-grow-1 py-3">
                        <i class="fas fa-file-csv me-2 text-primary"></i>CSV
                    </button>
                    <button class="btn btn-outline-secondary flex-grow-1 py-3">
                        <i class="fas fa-file-pdf me-2 text-danger"></i>PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('participantSearch');
        const table = document.getElementById('participantsTable');
        
        if (searchInput && table) {
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    
                    if (name.includes(searchValue) || email.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
@endsection