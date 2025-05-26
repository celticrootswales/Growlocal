<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">
            🌱 GrowLocal
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnavMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topnavMenu">
            <!-- Mobile-only nav -->
            <ul class="navbar-nav d-md-none me-auto">
                @role('grower')
                    <li class="nav-item">
                        <a href="{{ route('grower.dashboard') }}" class="nav-link">🏠 Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('grower.notes.index') }}" class="nav-link">📦 Delivery Notes</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('grower.delivery-notes.create') }}" class="nav-link">➕ New Delivery</a>
                    </li>
                @endrole
            </ul>

            <!-- Right user profile -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">Log out</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>