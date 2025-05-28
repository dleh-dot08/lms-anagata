<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="user-dashboard" class="app-brand-link">
            <span class="app-brand-logo demo">
                <!-- <div class="sidebar-header">
                    <img src="{{ asset('assets/img/illustrations/logo-asn.png') }}" alt="logo-asn" class="logo-image">
                </div> -->
            </span>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">master</span>
        </li>

        <li class="menu-item {{ request()->routeIs('users.*', 'jenjang.*', 'kategori.*', 'admin.faq.*', 'admin.sekolah.*', 'admin.kelas.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-menu"></i>
                <div data-i18n="Account Settings">Data Manajemen</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Pengguna</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('jenjang.*') ? 'active' : '' }}">
                    <a href="{{ route('jenjang.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Jenjang</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('program.*') ? 'active' : '' }}">
                    <a href="{{ route('program.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Program</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Kategori</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.faq.index') }}" class="menu-link">
                        <div data-i18n="Account">Data FAQ</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.kelas.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Kelas</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.sekolah.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sekolah.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Sekolah</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Course Management</span>
        </li>
        <li class="menu-item {{ request()->routeIs('courses.*') ? 'active' : '' }}">
            <a href="{{ route('courses.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Kursus</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
            <a href="{{ route('admin.projects.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Projects</div>
            </a>
        </li>
        <li class="menu-item {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
            <a href="{{ route('certificates.indexAdmin') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Sertifikat</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
    <span class="menu-header-text"> Activity Management</span>
</li>
<li class="menu-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
    <a href="{{ route('activities.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-file"></i>
        <div data-i18n="Boxicons">Data Kegiatan</div>
    </a>
</li>

<!-- Mentor Notes -->
<li class="menu-item {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
    <a href="{{ route('admin.notes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-notepad"></i>
        <div data-i18n="Boxicons">Mentor Notes</div>
    </a>
</li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text"> Attendance Management</span>
        </li>
        <li class="menu-item {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
            <a href="{{ route('attendances.admin.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Absensi</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text"> Report Management</span>
        </li>
        <li class="menu-item {{ request()->routeIs('admin.reports.nilai.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.nilai.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Laporan Nilai</div>
            </a>
        </li>
        
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text"> Helpdesk Management</span>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin.helpdesk.tickets.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Helpdesk</div>
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
