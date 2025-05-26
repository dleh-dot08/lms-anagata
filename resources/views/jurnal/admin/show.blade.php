@extends('layouts.admin.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-transparent" style="background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="bx bx-notepad fs-2 text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold">Catatan Mentor</h3>
                                <p class="mb-0 fs-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p>
                                <small class="opacity-75">
                                    <i class="bx bx-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') }}
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.notes.meetings', $meeting->course_id) }}" class="btn btn-light btn-sm">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <a href="#" onclick="window.print()" class="btn btn-light btn-sm ms-2">
                                <i class="bx bx-printer me-1"></i> Cetak
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course & Mentor Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bx bx-book text-primary me-2"></i> Informasi Kursus
                    </h5>
                    <div class="mb-2">
                        <small class="text-muted">Nama Kursus</small>
                        <p class="mb-1 fw-semibold">{{ $meeting->course->nama_kelas }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Kategori</small>
                        <p class="mb-1 fw-semibold">{{ $meeting->course->kategori->nama_kategori ?? 'Tidak ada kategori' }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Periode</small>
                        <p class="mb-0 fw-semibold">
                            {{ \Carbon\Carbon::parse($meeting->course->waktu_mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($meeting->course->waktu_akhir)->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bx bx-user text-success me-2"></i> Informasi Mentor
                    </h5>
                    <div class="mb-2">
                        <small class="text-muted">Nama Mentor</small>
                        <p class="mb-1 fw-semibold">{{ $meeting->course->mentor->name ?? 'Belum ditentukan' }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Email</small>
                        <p class="mb-1 fw-semibold">{{ $meeting->course->mentor->email ?? '-' }}</p>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted">Terakhir Diperbarui</small>
                        <p class="mb-0 fw-semibold">
                            {{ $note->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Title Card -->
    @if($meeting->judul)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-bookmark-alt text-primary me-3 fs-4"></i>
                        <div>
                            <h5 class="mb-0 fw-semibold">{{ $meeting->judul }}</h5>
                            <small class="text-muted">Topik Pertemuan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Note Content -->
    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4 note-cards">
        <!-- Materi -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-book me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Materi yang Disampaikan</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->materi)
                        <div class="note-content">
                            {{ $note->materi }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->materi) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan materi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-folder-open me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Project yang Dikerjakan</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->project)
                        <div class="note-content">
                            {{ $note->project }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->project) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan project</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sikap Siswa -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-group me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Sikap Siswa</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->sikap_siswa)
                        <div class="note-content">
                            {{ $note->sikap_siswa }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->sikap_siswa) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan sikap siswa</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hambatan -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-warning text-dark py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error-circle me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Hambatan</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->hambatan)
                        <div class="note-content">
                            {{ $note->hambatan }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->hambatan) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan hambatan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Solusi -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-bulb me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Solusi</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->solusi)
                        <div class="note-content">
                            {{ $note->solusi }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->solusi) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan solusi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Masukan -->
        <div class="col">
            <div class="card h-100 border-0 shadow-sm note-card">
                <div class="card-header bg-secondary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-comment-detail me-2 fs-5"></i>
                        <h5 class="mb-0 fw-bold">Masukan</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($note->masukan)
                        <div class="note-content">
                            {{ $note->masukan }}
                        </div>
                        <div class="text-end mt-2">
                            <small class="text-muted">
                                <i class="bx bx-text"></i> {{ strlen($note->masukan) }} karakter
                            </small>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-file text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Tidak ada catatan masukan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Note Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">
                        <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i> Ringkasan Catatan
                    </h5>
                    
                    @php
                        $fields = ['materi', 'project', 'sikap_siswa', 'hambatan', 'solusi', 'masukan', 'lain_lain'];
                        $filledFields = 0;
                        $totalCharCount = 0;
                        
                        foreach ($fields as $field) {
                            if (!empty($note->$field)) {
                                $filledFields++;
                                $totalCharCount += strlen($note->$field);
                            }
                        }
                        
                        $completionPercentage = round(($filledFields / count($fields)) * 100);
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                        <i class="bx bx-list-check text-primary fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $filledFields }} / {{ count($fields) }}</h6>
                                    <small class="text-muted">Bagian Terisi</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                        <i class="bx bx-pie-chart-alt text-success fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $completionPercentage }}%</h6>
                                    <small class="text-muted">Persentase Kelengkapan</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                        <i class="bx bx-text text-info fs-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $totalCharCount }}</h6>
                                    <small class="text-muted">Total Karakter</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label small text-muted">Progress Kelengkapan</label>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ $completionPercentage < 25 ? 'bg-danger' : ($completionPercentage < 50 ? 'bg-warning' : ($completionPercentage < 75 ? 'bg-info' : 'bg-success')) }}" 
                                role="progressbar" 
                                style="width: {{ $completionPercentage }}%" 
                                aria-valuenow="{{ $completionPercentage }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .note-card {
        transition: all 0.3s ease;
    }
    
    .note-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .note-content {
        white-space: pre-line;
        max-height: 200px;
        overflow-y: auto;
    }
    
    /* Custom scrollbar */
    .note-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .note-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .note-content::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }
    
    .note-content::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
    
    /* Print styles */
    @media print {
        .btn, .progress-bar-animated {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        
        .note-content {
            max-height: none;
            overflow: visible;
        }
        
        .container-fluid {
            padding: 0 !important;
        }
        
        .note-cards {
            display: block !important;
        }
        
        .note-card {
            margin-bottom: 20px !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection