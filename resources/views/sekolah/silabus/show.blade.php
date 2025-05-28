@extends('layouts.sekolah.template')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">
        <span class="text-dark">{{ $course->nama_kelas }}</span>
    </h3>
    
    <div class="mb-3">
        <a href="{{ route('sekolah.silabus.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Silabus
        </a>
    </div>
    
    @php
        $silabus_pdf = $course->silabus_pdf;
        $previewLink = convertToPreview($silabus_pdf);
    @endphp

    @if ($silabus_pdf)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Silabus
            </h5>
            <div>
                <a href="{{ $previewLink }}" target="_blank" class="btn btn-sm btn-dark">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Tab Baru
                </a>
                @if ($course->rpp_drive_link)
                    <a href="{{ $course->rpp_drive_link }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="bi bi-file-earmark-text me-1"></i>Lihat RPP
                    </a>
                @endif
            </div>            
        </div>
        <div class="card-body p-0">
            <iframe 
                src="{{ $previewLink }}" 
                width="100%" 
                height="600px" 
                frameborder="0"
                class="rounded-bottom"
                sandbox="allow-scripts allow-same-origin allow-presentation"
            ></iframe>
        </div>
    </div>
    @else
        <div class="alert alert-warning mt-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Silabus belum tersedia untuk kelas ini.
        </div>
    @endif
</div>
@endsection