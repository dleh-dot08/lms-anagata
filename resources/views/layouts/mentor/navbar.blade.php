<style>
    /* Custom styles for navbar text */
    .navbar-text {
        font-family: Arial, sans-serif;
        /* Use Arial font */
        font-size: 25px;
        /* Set font size to 18 pixels */
        font-weight: bold;
        /* Make font bold */
        color: #6d7de5;
        /* Change text color to red (you can use any color code or name) */
    }

    .navbar-text-second {
        font-family: Arial, sans-serif;
        /* Use Arial font */
        font-size: 15px;
        /* Set font size to 18 pixels */
        font-weight: bold;
        /* Make font bold */
        color: #acacac;
        /* Change text color to red (you can use any color code or name) */
    }

    /* Custom styles for logout button */
    .navbar-nav .nav-link {
        font-family: Arial, sans-serif;
        /* Use Arial font */
        font-size: 16px;
        /* Set font size to 16 pixels */
    }

    /* Custom styles for navbar padding */
    .navbar {
        padding-top: 0.5rem;
        /* Set padding top */
        padding-bottom: 0.5rem;
        /* Set padding bottom */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-white">
    <div class="container-fluid">
        <!-- Left side: Welcome message -->
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>
        <span class="navbar-text"><b>{{ Auth::user()->name }}</b></span>
        &nbsp;&nbsp;<small class="navbar-text-second">{{ Auth::user()->role->name }}</small>

        <!-- Right side: Notifications and Logout button -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="btn rounded-pill btn-primary cols"><i class="bx bx-power-off me-2"></i>Log Out</span>
                </a>
            </li>
        </ul>

        <!-- Logout form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</nav>

<script>
    document.getElementById('navbarDropdown').addEventListener('click', function() {
        // Hapus tanda (mark) hanya jika ada notifikasi yang belum dibaca
        let badge = this.querySelector('.badge');
        if (badge) {
            badge.style.display = 'none'; // Sembunyikan tanda setelah notifikasi dibaca
        }
    });
</script>
