<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('mentor.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <!-- <div class="sidebar-header">
                    <img src="{{ asset('assets/img/illustrations/logo-asn.png') }}" alt="logo-asn" class="logo-image">
                </div> -->
            </span>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
            <a href="{{ route('mentor.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('biodata.*') ? 'active' : '' }}">
            <a href="{{ route('biodata.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div data-i18n="Analytics">Biodata</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('mentor.kursus.index') ? 'active' : '' }}">
            <a href="{{ route('mentor.kursus.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-book-alt"></i>
                <div data-i18n="Analytics">Kursus</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('attendances.courses', 'attendances.activities') ? 'open active' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxs-calendar-check"></i>
                <div data-i18n="Analytics">Absensi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('attendances.courses') ? 'active' : '' }}">
                    <a href="{{ route('attendances.courses') }}" class="menu-link">
                        <div>Absen Kursus</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('attendances.activities') ? 'active' : '' }}">
                    <a href="{{ route('attendances.activities') }}" class="menu-link">
                        <div>Absen Kegiatan</div>
                    </a>
                </li>
            </ul>
        </li>        


        <!-- Helpdesk -->
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
