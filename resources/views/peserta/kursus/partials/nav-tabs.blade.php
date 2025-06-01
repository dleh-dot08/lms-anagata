@php
    $activeTab = $activeTab ?? '';
@endphp

<div class="card border-0 shadow-sm rounded-3 mb-4 course-tabs-container">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto scrollbar-hidden py-2 px-3">
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'overview' ? 'active' : '' }}" 
                   href="{{ route('peserta.kursus.overview', $course->id) }}">
                   <i class="bi bi-info-circle-fill me-2"></i>Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'meetings' ? 'active' : '' }}" 
                   href="{{ route('peserta.kursus.meetings', $course->id) }}">
                   <i class="bi bi-calendar-event-fill me-2"></i>Materi & Video
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'assignment' ? 'active' : '' }}" 
                   href="{{ route('peserta.kursus.assignments', $course->id) }}">
                   <i class="bi bi-file-earmark-ruled-fill me-2"></i>Tugas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'project' ? 'active' : '' }}" 
                   href="{{ route('peserta.kursus.projects', $course->id) }}">
                   <i class="bi bi-folder-fill me-2"></i>Project
                </a>
            </li>
        </ul>
    </div>
</div>

@push('styles')
<style>
:root {
    --purple-main: #6f42c1;
    --purple-dark: #5e35b1;
    --purple-light: #9c27b0;
    --purple-lighter: #e1bee7;
    --purple-lightest: #f3e5f5;
}

.course-tabs-container {
    background: white;
    border-radius: 0.5rem;
}

.custom-tab-link {
    color: var(--bs-gray-700);
    border-radius: 0.375rem;
    padding: 0.75rem 1.25rem;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
}

.custom-tab-link:hover {
    color: var(--purple-main);
    background-color: var(--purple-lightest);
}

.custom-tab-link.active {
    background-color: var(--purple-main) !important;
    color: white !important;
}

.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}

.scrollbar-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endpush