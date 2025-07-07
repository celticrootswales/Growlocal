<nav class="navbar navbar-expand-md bg-white fixed-top shadow-sm">
    <div class="container-fluid">
        {{-- Sidebar toggle (Desktop) --}}
        <button class="btn btn-outline-secondary d-none d-md-inline me-3" id="sidebarToggle">
            <i class="bi bi-list" style="font-size:18px;"></i>
        </button>

        {{-- Brand/logo --}}
        <a class="navbar-brand fs-3" style="color: #23c1b8 !important;" href="#">
            <i class="bi bi-leaf-fill"></i> Growlocal
        </a>

        {{-- Mobile burger --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible Right Side --}}
        <div class="collapse navbar-collapse justify-content-end" id="topNavDropdown">
            @auth
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">⚙️ Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">🚪 Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>