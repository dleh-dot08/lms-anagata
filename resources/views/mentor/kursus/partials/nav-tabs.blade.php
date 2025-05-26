@php
    $activeTab = $activeTab ?? '';
@endphp

<div class="card border-0 shadow-sm rounded-3 mb-4 course-tabs-container">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto scrollbar-hidden py-2 px-3">
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'overview' ? 'active' : '' }}" 
                   href="{{ route('kursus.mentor.overview', $course->id) }}">
                   <i class="bi bi-info-circle-fill me-2"></i>Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'meetings' ? 'active' : '' }}" 
                   href="{{ route('mentor.kursus.show', $course->id) }}">
                   <i class="bi bi-calendar-event-fill me-2"></i>Meetings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'silabus' ? 'active' : '' }}" 
                   href="{{ route('kursus.mentor.silabus', $course->id) }}">
                   <i class="bi bi-journal-text me-2"></i>Silabus
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'assignment' ? 'active' : '' }}" 
                   href="{{ route('kursus.mentor.assignment', $course->id) }}">
                   <i class="bi bi-file-earmark-ruled-fill me-2"></i>Assignment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'scores' ? 'active' : '' }}" 
                   href="{{ route('mentor.scores.index', $course->id) }}">
                   <i class="bi bi-graph-up-arrow me-2"></i>Scores
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'project' ? 'active' : '' }}" 
                   href="{{ route('kursus.mentor.project', $course->id) }}">
                   <i class="bi bi-folder-fill me-2"></i>Project
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab-link {{ $activeTab === 'peserta' ? 'active' : '' }}" 
                   href="{{ route('kursus.mentor.peserta', $course->id) }}">
                   <i class="bi bi-people-fill me-2"></i>Peserta
                </a>
            </li>
        </ul>
    </div>
</div>

@push('styles')
<style>
/* Define a custom color palette (should be consistent with your main template) */
:root {
    --purple-main: #6f42c1; /* A primary purple */
    --purple-dark: #5e35b1; /* Darker purple for accents */
    --purple-light: #9c27b0; /* Lighter purple */
    --purple-lighter: #e1bee7; /* Very light purple */
    --purple-lightest: #f3e5f5; /* Almost white purple for table group headers */

    --primary: #0d6efd; /* Bootstrap primary blue */
    /* ... other colors ... */
}

/* Container for the tabs */
.course-tabs-container {
    background-color: #fff; /* Ensure white background for the card */
}

/* Custom styling for nav-pills */
.nav-pills .nav-item {
    margin: 0.25rem; /* Add some margin between pills */
}

.nav-pills .nav-link {
    font-weight: 600;
    color: var(--dark); /* Default text color */
    padding: 0.75rem 1.25rem; /* More generous padding */
    border-radius: 0.5rem; /* Slightly rounded corners */
    transition: all 0.3s ease-in-out; /* Smooth transitions */
    background-color: var(--light); /* Light background for non-active tabs */
    box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Subtle shadow */
    display: flex; /* Enable flex for icon and text alignment */
    align-items: center;
    justify-content: center; /* Center content horizontally */
    min-width: 120px; /* Ensure minimum width for each tab */
    text-align: center; /* Center text */
}

.nav-pills .nav-link:hover {
    color: var(--purple-main); /* Hover text color */
    background-color: var(--purple-lighter); /* Lighter purple on hover */
    transform: translateY(-2px); /* Slight lift effect */
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* More pronounced shadow on hover */
}

.nav-pills .nav-link.active {
    color: #fff; /* Active text color */
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%); /* Purple gradient for active tab */
    box-shadow: 0 4px 10px rgba(0,0,0,0.2); /* Stronger shadow for active tab */
    transform: translateY(0); /* Reset lift if active */
    border: none; /* Ensure no border */
}

.nav-pills .nav-link.active:hover {
    color: #fff; /* Keep text white on hover for active */
    background: linear-gradient(135deg, var(--purple-main) 0%, var(--purple-dark) 100%); /* Keep gradient */
    transform: translateY(-1px); /* Slight lift on active hover */
}

/* Hide scrollbar but allow scrolling on small screens */
.scrollbar-hidden {
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
}
.scrollbar-hidden::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
}

/* Responsive adjustments for smaller screens */
@media (max-width: 767.98px) {
    .nav-pills {
        padding-bottom: 0.5rem !important; /* Add padding at bottom for scrollbar (if visible) */
    }
    .nav-pills .nav-link {
        padding: 0.6rem 0.8rem; /* Smaller padding */
        font-size: 0.9rem; /* Smaller font size */
        min-width: 100px; /* Adjust min width */
    }
    .nav-pills .nav-link i {
        margin-right: 0.5rem; /* Adjust icon spacing */
    }
    .nav-pills.flex-nowrap {
        overflow-x: auto; /* Enable horizontal scrolling */
    }
}
</style>
@endpush