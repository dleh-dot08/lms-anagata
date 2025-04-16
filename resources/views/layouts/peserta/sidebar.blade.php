<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('peserta.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <div class="sidebar-header">
                    <img src="{{ asset('assets/img/illustrations/logo-asn.png') }}" alt="logo-asn" class="logo-image">
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
        <li class="menu-item {{ Request::is('peserta/dashboard') ? 'active' : '' }}">
            <a href="{{ route('peserta.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Biodata -->
        <li class="menu-item {{ Request::is('biodata*') ? 'active' : '' }}">
            <a href="{{ route('biodata.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div data-i18n="Analytics">Biodata</div>
            </a>
        </li>


        <!-- Kursus -->
        <li class="menu-item {{ Request::is('peserta/kursus*') ? 'active' : '' }}">
            <a href="{{ route('courses.indexpeserta') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-book-alt"></i>
                <div data-i18n="Analytics">Kursus</div>
            </a>
        </li>

        <!-- Absensi -->
        <li class="menu-item {{ Request::is('courses/absensi') ? 'active' : '' }}">
            <a href="{{ route('courses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-calendar-check"></i>
                <div data-i18n="Analytics">Absensi</div>
            </a>
        </li>

        <!-- Sertifikat -->
        <li class="menu-item {{ Request::is('courses/sertifikat') ? 'active' : '' }}">
            <a href="{{ route('courses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-badge-check"></i>
                <div data-i18n="Analytics">Sertifikat</div>
            </a>
        </li>

        <!-- Helpdesk -->
        <li class="menu-item {{ Request::is('helpdesk') ? 'active' : '' }}">
            <a href="{{ route('peserta.helpdesk.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-badge-check"></i>
                <div data-i18n="Analytics">Helpdesk</div>
            </a>
        </li>
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
