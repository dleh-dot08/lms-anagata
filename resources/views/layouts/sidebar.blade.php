<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="admin-dashboard" class="app-brand-link">
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
        <li class="menu-item active">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">master</span>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-menu"></i>
                <div data-i18n="Account Settings">Data Manajemen</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('divisi.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Divisi</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('jabatan.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Jabatan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('tujuan.index') }}" class="menu-link">
                        <div data-i18n="Account">Data Tujuan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('instansi.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Data Instansi</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sekolah.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Data Sekolah</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('users.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Data User</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('jenis_cuti.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Data Jenis Cuti</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('unit_penempatan.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Data Unit Penempatan</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('karyawan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Master Karyawan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('mentor.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Master Mentor</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Arsip</span>
        </li>

        <li class="menu-item">
            <a href="{{ route('jenjang.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Jenjang</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('kategori.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Kategori</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('sub_kategori.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Boxicons">Data Sub-Kategori</div>
            </a>
        </li>

        <!-- Components -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Administrasi</span></li>

        <!-- User interface -->
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div data-i18n="User interface">Surat Menyurat</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('surat_masuk.index') }}" class="menu-link">
                        <div data-i18n="Accordion">Surat Masuk</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('surat-keluar.index') }}" class="menu-link">
                        <div data-i18n="Alerts">Surat Keluar</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Extended components -->
        <li class="menu-item">
            <a href="{{ route('berita_acara.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="Boxicons">Berita Acara</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('berita-acara.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="Boxicons">Berita Acara New</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('release.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bookmark"></i>
                <div data-i18n="Boxicons">Release</div>
            </a>
        </li>

        <!-- Forms & Tables -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Human Resource</span></li>
        <li class="menu-item">
            <a href="{{ route('data_cuti.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-detail"></i>
                <div data-i18n="Boxicons">Data Pengajuan Cuti</div>
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
        /* Ensure it doesn't overflow the container */
        width: 80%;
        /* Adjust this value as needed */
        height: auto;
        /* Maintain the aspect ratio */
    }
</style>
