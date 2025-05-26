<div id="sidebar" class="bg-success text-white p-3 vh-100 d-flex flex-column" style="width: 250px;">
    <h5 class="mb-4 fw-bold">🌿 GrowLocal</h5>
    <ul class="nav flex-column">
        @auth
            @role('grower')
                <li class="nav-item mb-2">
                    <a href="{{ route('grower.dashboard') }}" class="nav-link text-white {{ request()->routeIs('grower.dashboard') ? 'fw-bold' : '' }}">
                        🏠 Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('grower.notes.index') }}" class="nav-link text-white {{ request()->routeIs('grower.notes.index') ? 'fw-bold' : '' }}">
                        📦 Delivery Notes
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('grower.delivery-notes.create') }}" class="nav-link text-white {{ request()->routeIs('grower.delivery-notes.create') ? 'fw-bold' : '' }}">
                        ➕ New Delivery
                    </a>
                </li>
            @elserole('admin')
                <li class="nav-item mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'fw-bold' : '' }}">
                        🛠 Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('admin.notes') }}" class="nav-link text-white {{ request()->routeIs('admin.notes') ? 'fw-bold' : '' }}">
                        📄 Review Notes
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="{{ route('admin.recalls') }}" class="nav-link text-white {{ request()->routeIs('admin.recalls') ? 'fw-bold' : '' }}">
                        ⚠️ Manage Recalls
                    </a>
                </li>
            @endrole
            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-light w-100">Logout</button>
                </form>
            </li>
        @endauth
    </ul>
</div>