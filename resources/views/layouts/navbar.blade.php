    <!-- Bootstrap CSS -->

    <!-- jQuery and Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top py-5 d-block"
        data-navbar-on-scroll="data-navbar-on-scroll">
        <div class="container"><a class="navbar-brand" href="#">

                <!-- Generator: Adobe Illustrator 24.0.2, SVG Export Plug-In . SVG Version: 6.00 Build 0)  -->
                <img src="{{ asset('assets/img/icons/logo-asn.png') }}" width="150" alt="logo" />
                <img src="{{ asset('assets/img/icons/logo-anagata-academy.png') }}" width="100" alt="logo" />
                <img src="{{ asset('assets/img/icons/logo-codingmu.png') }}" width="150" alt="logo" />
                
            </a><button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"> </span></button>
                <div class="collapse navbar-collapse border-top border-lg-0 mt-4 mt-lg-0" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto pt-2 pt-lg-0 font-base align-items-lg-center align-items-start">
                        {{-- <li class="nav-item px-3 px-xl-4"><a class="nav-link fw-medium" aria-current="page" href="#Daftar Kursus">Kategori</a></li> --}}

                        @if (Route::has('login'))
                            @if (Auth::check())
                                @php
                                    $role = Auth::user()->role_id;
                                    $homeRoute = match ($role) {
                                        1 => route('admin.dashboard'),
                                        2 => route('mentor.dashboard'),
                                        3 => route('peserta.dashboard'),
                                        4 => route('karyawan.dashboard'),
                                        6 => route('sekolah.dashboard'),
                                        default => route('user.dashboard'),
                                    };
                                @endphp
                                <li class="nav-item px-3 px-xl-4">
                                    <a class="btn btn-outline-dark order-1 order-lg-0 fw-medium" aria-current="page"
                                        href="{{ $homeRoute }}">
                                        Home
                                    </a>
                                </li>
                            @else
                                <li class="nav-item px-3 px-xl-1">
                                    <a class="btn btn-outline-dark order-1 order-lg-0 fw-medium" href="{{ url('/faq') }}">FAQ</a>
                                </li>
                                <li class="nav-item px-3 px-xl-1">
                                    <a class="btn btn-outline-dark order-1 order-lg-0 fw-medium" href="{{ url('/login') }}">Login</a>
                                </li>
                                <li class="nav-item px-3 px-xl-1">
                                    <a class="btn btn-outline-dark order-1 order-lg-0 fw-medium" href="{{ url('/register') }}">Sign Up</a>
                                </li>
                            @endif
                        @endif

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius:0.3rem;"
                            aria-labelledby="navbarDropdown">
                        </ul>
                    </ul>
                </div>

        </div>
    </nav>
    <section style="padding-top: 7rem;">
        @if (!Request::is('faq')) {{-- or use Route::currentRouteName() if named --}}
            <div class="bg-holder" style="background-image: url('{{ asset('asset/img/hero/hero-bg.svg') }}');"></div>
        @endif

