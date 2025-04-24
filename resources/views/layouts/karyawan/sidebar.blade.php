<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('karyawan.dashboard') }}" class="app-brand-link">
            <div class="sidebar-header">
                <img src="{{ asset('assets/img/illustrations/logo-asn.png') }}" alt="logo-asn" class="logo-image">
            </div>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $divisi = auth()->user()->divisi;  // misal 'APD', 'MRC', atau 'Help Desk'
    @endphp

    <ul class="menu-inner py-1">
        {{-- Dashboard --}}
        <li class="menu-item {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
            <a href="{{ route('karyawan.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        {{-- Biodata --}}
        <li class="menu-item {{ request()->routeIs('biodata.*') ? 'active' : '' }}">
            <a href="{{ route('biodata.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bxs-user-account"></i>
                <div data-i18n="Biodata">Biodata</div>
            </a>
        </li>

        {{-- APD: Kursus --}}
        @if($divisi === 'APD')
            <li class="menu-item {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <a href="{{ route('courses.apd.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-open"></i>
                    <div data-i18n="Kursus">Kursus</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                <a href="{{ route('activities.apd.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <div data-i18n="Kegiatan">Kegiatan</div>
                </a>
            </li>
        @endif

        {{-- MRC: FAQ --}}
        @if($divisi === 'MRC')
            <li class="menu-item {{ request()->routeIs('faqs.*') ? 'active' : '' }}">
                <a href="{{ route('faqs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-help-circle"></i>
                    <div data-i18n="FAQ">FAQ</div>
                </a>
            </li>
        @endif

        {{-- Help Desk: Helpdesk --}}
        @if($divisi === 'Help Desk')
            <li class="menu-item {{ request()->routeIs('helpdesk.*') ? 'active' : '' }}">
                <a href="{{ route('helpdesk.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-support"></i>
                    <div data-i18n="Helpdesk">Helpdesk</div>
                </a>
            </li>
        @endif
    </ul>
</aside>

<style>
    .app-brand {
        text-align: center;
        margin: 20px 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .logo-image {
        width: 80%;
        height: auto;
    }
</style>