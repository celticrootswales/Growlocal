
<nav class="navbar navbar-expand-md bg-white bg-success fixed-top shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-light d-none d-md-inline me-3" id="toggleSidebar">☰</button>
        <a style="color: rgb(159 75 205) !important;" href="#"><i class="bi bi-leaf-fill"></i> GrowLocal</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topNavDropdown">
            <ul class="navbar-nav d-md-none"> {{-- Mobile Nav Only --}}
                @include('layouts.nav-links')
            </ul>

            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ Auth::user()->name }}</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="{{ route('profile.edit') }}" class="dropdown-item">⚙️ Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">🚪 Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
<nav class="navbar navbar-expand-md bg-white bg-success fixed-top shadow-sm">
    <div class="container-fluid">

        {{-- App Title or Logo --}}
        <a class="navbar-brand fs-3 " style="color:rgb(159 75 205) !important;" href="#">
            <i class="bi bi-leaf-fill ml-3"></i> Welsh Veg In Schools
        </a>

        {{-- Mobile Menu Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavDropdown" aria-controls="topNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Collapsible Right Side --}}
        <div class="collapse navbar-collapse justify-content-end" id="topNavDropdown">
            @auth
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a id="userDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">⚙️ Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">🚪 Logout</button>
                            </form>
                        </div>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>