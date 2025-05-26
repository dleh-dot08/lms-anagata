@extends('layouts.mentor.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="bi bi-journal-text fs-2 text-dark"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold">Catatan Pertemuan</h3>
                                <p class="mb-0 fs-5 opacity-90">Pertemuan ke-{{ $meeting->pertemuan }}</p>
                                <small class="opacity-75">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $meeting->tanggal_pelaksanaan ? \Carbon\Carbon::parse($meeting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') : 'Tanggal belum ditentukan' }}
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-light btn-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>
                                Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Title Card -->
    @if($meeting->judul)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bookmark-fill text-primary me-3 fs-4"></i>
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

    <!-- Notes Content -->
    <div class="row">
        <div class="col-12">
            @php
                $fields = [
                    'materi' => [
                        'label' => 'Materi yang Disampaikan', 
                        'icon' => 'bi-book',
                        'color' => 'primary',
                        'bg_color' => 'bg-primary'
                    ],
                    'project' => [
                        'label' => 'Project yang Dikerjakan', 
                        'icon' => 'bi-folder2-open',
                        'color' => 'success',
                        'bg_color' => 'bg-success'
                    ],
                    'sikap_siswa' => [
                        'label' => 'Sikap Siswa', 
                        'icon' => 'bi-people',
                        'color' => 'info',
                        'bg_color' => 'bg-info'
                    ],
                    'hambatan' => [
                        'label' => 'Hambatan', 
                        'icon' => 'bi-exclamation-triangle',
                        'color' => 'warning',
                        'bg_color' => 'bg-warning'
                    ],
                    'solusi' => [
                        'label' => 'Solusi', 
                        'icon' => 'bi-lightbulb',
                        'color' => 'success',
                        'bg_color' => 'bg-success'
                    ],
                    'masukan' => [
                        'label' => 'Masukan', 
                        'icon' => 'bi-chat-left-text',
                        'color' => 'secondary',
                        'bg_color' => 'bg-secondary'
                    ],
                    'lain_lain' => [
                        'label' => 'Lain-lain', 
                        'icon' => 'bi-three-dots',
                        'color' => 'dark',
                        'bg_color' => 'bg-dark'
                    ],
                ];
            @endphp

            <div class="row g-4">
                @foreach($fields as $key => $field)
                <div class="col-lg-6 col-12">
                    <div class="card border-0 shadow-sm h-100 note-card">
                        <div class="card-header border-0 {{ $field['bg_color'] }} text-white py-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper me-3">
                                    <i class="bi {{ $field['icon'] }} fs-5"></i>
                                </div>
                                <h6 class="mb-0 fw-semibold">{{ $field['label'] }}</h6>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if(empty($note->$key))
                                <div class="text-center py-4">
                                    <i class="bi bi-file-text text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted fst-italic mb-0">Belum ada catatan</p>
                                </div>
                            @else
                                <div class="note-content">
                                    <p class="mb-0 lh-base" style="white-space: pre-line;">{{ $note->$key }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Character count for non-empty fields -->
                        @if(!empty($note->$key))
                        <div class="card-footer border-0 bg-transparent py-2">
                            <small class="text-muted">
                                <i class="bi bi-type me-1"></i>
                                {{ strlen($note->$key) }} karakter
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body py-4">
                    <h5 class="mb-3 fw-bold text-center">
                        <i class="bi bi-graph-up me-2"></i>
                        Ringkasan Catatan
                    </h5>
                    <div class="row text-center g-3">
                        @php
                            $totalFields = count($fields);
                            $filledFields = 0;
                            $totalCharacters = 0;
                            
                            foreach($fields as $key => $field) {
                                if(!empty($note->$key)) {
                                    $filledFields++;
                                    $totalCharacters += strlen($note->$key);
                                }
                            }
                            
                            $completionPercentage = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
                        @endphp
                        
                        <div class="col-md-3 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <i class="bi bi-check2-circle text-success fs-2"></i>
                                </div>
                                <h4 class="mb-0 fw-bold text-success">{{ $filledFields }}</h4>
                                <small class="text-muted">Bagian Terisi</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <i class="bi bi-list-ul text-primary fs-2"></i>
                                </div>
                                <h4 class="mb-0 fw-bold text-primary">{{ $totalFields }}</h4>
                                <small class="text-muted">Total Bagian</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <i class="bi bi-percent text-info fs-2"></i>
                                </div>
                                <h4 class="mb-0 fw-bold text-info">{{ $completionPercentage }}%</h4>
                                <small class="text-muted">Kelengkapan</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex flex-column align-items-center">
                                <div class="mb-2">
                                    <i class="bi bi-type text-secondary fs-2"></i>
                                </div>
                                <h4 class="mb-0 fw-bold text-secondary">{{ number_format($totalCharacters) }}</h4>
                                <small class="text-muted">Total Karakter</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">Kelengkapan Catatan</span>
                            <span class="small text-muted">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar 
                                @if($completionPercentage >= 80) bg-success
                                @elseif($completionPercentage >= 60) bg-info  
                                @elseif($completionPercentage >= 40) bg-warning
                                @else bg-danger
                                @endif" 
                                style="width: {{ $completionPercentage }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('mentor.notes.index', $meeting->course_id) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Daftar Pertemuan
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>
                Cetak Catatan
            </button>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .note-card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .note-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .icon-wrapper {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        border-radius: 8px;
    }
    
    .note-content {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 5px;
    }
    
    .note-content::-webkit-scrollbar {
        width: 4px;
    }
    
    .note-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    
    .note-content::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 2px;
    }
    
    .note-content::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
    }
    
    @media print {
        .btn, .card-footer {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
            margin-bottom: 1rem;
        }
        
        .note-card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

<!-- Animation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards on load
        const cards = document.querySelectorAll('.note-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Animate progress bar
        setTimeout(() => {
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.width = width;
                }, 500);
            }
        }, 1000);
    });
</script>

@endsection