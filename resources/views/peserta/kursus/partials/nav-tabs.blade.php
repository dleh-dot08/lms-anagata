@php
    $activeTab = $activeTab ?? '';
@endphp

<div class="card border-0 shadow-sm rounded-3 mb-4 course-tabs-container animated fadeInDown">
    <div class="card-body p-0">
        <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto scrollbar-hidden py-2 px-3">
            <li class="nav-item flex-grow-0 flex-shrink-0 mx-1 mb-1">
                <a class="nav-link custom-tab-link {{ $activeTab === 'overview' ? 'active' : '' }}"
                   href="{{ route('peserta.kursus.overview', $course->id) }}">
                   <i class="bi bi-info-circle-fill"></i>
                   <span>Overview</span>
                </a>
            </li>

            <li class="nav-item flex-grow-0 flex-shrink-0 mx-1 mb-1">
                <a class="nav-link custom-tab-link {{ $activeTab === 'meetings' ? 'active' : '' }}"
                   href="{{ route('peserta.kursus.meetings', $course->id) }}">
                   <i class="bi bi-calendar-event-fill"></i>
                   <span>Materi & Video</span>
                </a>
            </li>

            <li class="nav-item flex-grow-0 flex-shrink-0 mx-1 mb-1">
                <a class="nav-link custom-tab-link {{ $activeTab === 'assignment' ? 'active' : '' }}"
                   href="{{ route('peserta.kursus.assignments', $course->id) }}">
                   <i class="bi bi-file-earmark-ruled-fill"></i>
                   <span>Tugas</span>
                </a>
            </li>

            <li class="nav-item flex-grow-0 flex-shrink-0 mx-1 mb-1">
                <a class="nav-link custom-tab-link {{ $activeTab === 'project' ? 'active' : '' }}"
                   href="{{ route('peserta.kursus.projects', $course->id) }}">
                   <i class="bi bi-folder-fill"></i>
                   <span>Project</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<style>
/* CSS Variables (assuming Bootstrap 5 is in use) */
:root {
    --blue-main: #0d6efd; /* Bootstrap's primary blue */
    --blue-dark: #0a58ca;
    --blue-light: #6ea8fe;
    --blue-lighter: #cfe2ff;
    --blue-lightest: #e9f2ff; /* A very light blue for subtle backgrounds */
    --bs-gray-700: #495057; /* Bootstrap's gray-700 */
}

/* Main Container for Tabs */
.course-tabs-container {
    background: white;
    border-radius: 0.75rem; /* Slightly more rounded */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Enhanced shadow for depth */
}

/* Custom Tab Link Styling */
.custom-tab-link {
    color: var(--bs-gray-700);
    border-radius: 0.5rem; /* Slightly more rounded corners for links */
    padding: 0.75rem 1.25rem; /* Consistent padding */
    transition: all 0.2s ease-in-out;
    white-space: nowrap; /* Prevents text wrapping */
    font-weight: 500; /* Slightly bolder font for better readability */
    
    display: flex; /* Use flexbox for icon and text alignment */
    align-items: center; /* Center icon and text vertically */
    justify-content: center; /* Center content horizontally within the tab */
    text-align: center; /* Fallback for text centering */
    flex-direction: row; /* Default: icon and text side-by-side */
}

/* Spacing between icon and text for desktop */
.custom-tab-link i {
    font-size: 1.1em; /* Slightly larger icons */
    margin-right: 0.5rem; /* Space between icon and text in a row layout */
}

.custom-tab-link:hover {
    color: var(--blue-main); /* Hover text color */
    background-color: var(--blue-lightest); /* Hover background color */
    transform: translateY(-2px); /* Subtle lift on hover */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); /* Subtle shadow on hover */
}

.custom-tab-link.active {
    background-color: var(--blue-main) !important; /* Active background color */
    color: white !important; /* Active text color */
    font-weight: 600; /* Bolder for active state */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* More prominent shadow for active state */
    transform: translateY(0); /* Reset transform */
}

/* Hide Scrollbar */
.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}

.scrollbar-hidden {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}

/* Animations */
.animated {
    animation-duration: 0.8s;
    animation-fill-mode: both;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translate3d(0, -20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.fadeInDown {
    animation-name: fadeInDown;
}

/* Responsive Adjustments */
@media (max-width: 767.98px) { /* Small devices (phones) */
    .nav-fill .nav-item {
        flex: 0 0 auto; /* Prevent nav-fill from stretching items */
        width: auto; /* Allow items to take only needed width */
    }

    .custom-tab-link {
        padding: 0.75rem 0.6rem; /* Reduced padding for smaller screens */
        font-size: 0.8rem; /* Smaller font size */
        flex-direction: column; /* Stack icon and text vertically */
        gap: 0.25rem; /* Small gap between icon and text */
        min-width: 75px; /* Minimum width for each tab to prevent extreme squishing */
    }

    /* Remove margin-right for icon when stacked vertically */
    .custom-tab-link i {
        margin-right: 0;
    }
}
</style>
