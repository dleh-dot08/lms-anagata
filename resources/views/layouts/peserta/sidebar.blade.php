<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('peserta.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <!-- <div class="sidebar-header">
                    <img src="{{ asset('assets/img/illustrations/logo-asn.png') }}" alt="logo-asn" class="logo-image">
                </div> -->
                <div class="sidebar-header">
                    <h3 class="text-primary mb-0" style="font-weight: 700; letter-spacing: 1px;">ruanganagata.id</h3>
                    <small class="text-muted">Official Website</small>
                </div>
            </span>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
            <a href="{{ route('peserta.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-dashboard"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('biodata.*') ? 'active' : '' }}">
            <a href="{{ route('biodata.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-detail"></i>
                <div data-i18n="Analytics">Biodata</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('courses.indexpeserta') ? 'active' : '' }}">
            <a href="{{ route('courses.indexpeserta') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-graduation"></i>
                <div data-i18n="Analytics">Kursus</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('projects.peserta.index') ? 'active' : '' }}">
            <a href="{{ route('projects.peserta.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-briefcase-alt-2"></i>
                <div data-i18n="Analytics">Project</div>
            </a>
        </li>
        {{-- <li class="menu-item {{ request()->routeIs('assignments.index') ? 'active' : '' }}">
            <a href="{{ route('assignments.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-file-doc"></i>
                <div data-i18n="Analytics">Assignment</div>
            </a>
        </li> --}}
        <li class="menu-item {{ request()->routeIs('attendances.courses', 'attendances.activities') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-calendar-check"></i>
                <div data-i18n="Analytics">Absensi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('attendances.activities') ? 'active' : '' }}">
                    <a href="{{ route('attendances.activities') }}" class="menu-link">
                        <div>Absen Kegiatan</div>
                    </a>
                </li>
            </ul>
        </li>        

        <li class="menu-item {{ request()->routeIs('certificates.indexPeserta') ? 'active' : '' }}">
            <a href="{{ route('certificates.indexPeserta') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-certification"></i>
                <div data-i18n="Analytics">Sertifikat</div>
            </a>
        </li>

        <!-- Helpdesk -->
        {{-- <li class="menu-item {{ Request::is('helpdesk') ? 'active' : '' }}">
            <a href="{{ route('peserta.helpdesk.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-help-circle"></i>
                <div data-i18n="Analytics">Helpdesk</div>
            </a>
        </li> --}}
    </ul>
</aside>

<style>
    .app-brand {
        text-align: center;
        margin-bottom: 20px;
        margin-top: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .logo-image {
        max-width: 100%;
        width: 80%;
        height: auto;
    }
</style>
