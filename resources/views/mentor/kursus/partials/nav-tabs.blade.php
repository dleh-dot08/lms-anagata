@php
    $activeTab = $activeTab ?? '';
@endphp

<ul class="nav nav-tabs mb-3 mt-3">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="{{ route('kursus.mentor.overview', $course->id) }}">Overview</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'meetings' ? 'active' : '' }}" href="{{ route('mentor.kursus.show', $course->id) }}">Meetings</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'silabus' ? 'active' : '' }}" href="{{ route('kursus.mentor.silabus', $course->id) }}">Silabus</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'assignment' ? 'active' : '' }}" href="{{ route('kursus.mentor.assignment', $course->id) }}">Assignment</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'scores' ? 'active' : '' }}" href="{{ route('mentor.scores.index', $course->id) }}">Score</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'project' ? 'active' : '' }}" href="{{ route('kursus.mentor.project', $course->id) }}">Project</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'peserta' ? 'active' : '' }}" href="{{ route('kursus.mentor.peserta', $course->id) }}">Peserta</a>
    </li>
</ul>
