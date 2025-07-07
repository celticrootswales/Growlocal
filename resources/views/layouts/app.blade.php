<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GrowLocal') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>
        body { padding-top: 56px; }


    body {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #f9fafa;
        color: var(--text-dark);
    }

    .nav-link.active,
    .nav-link:hover {
        font-weight: 500;
        background: #f0f0f0;
        border-radius: 4px;
    }

    /* Typography */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 600;
        margin: 0 0 0.5rem;
    }

    a {
        color: #2563eb; /* Tailwind blue-600 */
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
    }

th, td {
    padding: 0.75rem;
    border: 1px solid #e5e7eb; /* Tailwind gray-200 */
    text-align: left;
}



#mainSidebar {
    width: 250px;
    transition: width 0.3s ease;
    position: fixed;
    top: 68px; /* offset under topnav */
    bottom: 0;
    left: 0;
    z-index: 1030;
}

#mainSidebar.collapsed {
    width: 0;
    overflow: hidden;
}

#mainContent {
    margin-left: 250px;
    transition: margin-left 0.3s ease;
    width: 100%;
}

#mainContent h1 {
    font-size: 50px;
}

#mainSidebar.collapsed + #mainContent {
    margin-left: 0;
}

.overlay-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1025;
    display: none;
}

.header-blue {
    background: #7d93a9 !important
}


/* Custom Cards */
.card-green {
    background-color: var(--color-green-light);
    color: var(--text-dark);
}

.card-blue {
    background-color: var(--color-blue-light);
    color: var(--text-dark);
}

.card-yellow {
    background-color: var(--color-yellow-light);
    color: var(--text-dark);
}

/* Alerts */
.alert-danger {
    background-color: var(--color-danger);
    color: var(--text-dark);
}

/* Utilities */
.text-success {
    color: rgb(4 184 100) !important;
}

/* Term Badge Styling */
.term-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
    margin-right: 0.5rem;
}

.term-Autumn {
    background-color: #e67300;
}
.term-Spring {
    background-color: #28a745;
}
.term-Summer {
    background-color: #ffc107;
}
.term-FoodandFun {
    background-color: #6f42c1;
}

@media (max-width: 767.98px) {
    #mainSidebar {
        width: 250px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        display: block !important;
    }

    #mainSidebar.show {
        transform: translateX(0);
    }

    #mainContent {
        margin-left: 0 !important;
    }
}
    </style>
</head>
<body>
    <body>
    {{-- Only show nav/topnav/sidebar for authenticated users --}}
    @auth
        @include('layouts.topnav')
        <div class="d-flex">
            @include('layouts.sidebar')
            <div id="mainContent">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
        <div class="overlay-backdrop" id="sidebarBackdrop"></div>
    @endauth

    {{-- Show content only (no navs) for guests --}}
    @guest
        <div>
            @yield('content')
        </div>
    @endguest

    <div class="overlay-backdrop" id="sidebarBackdrop"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('mainSidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar && toggleBtn) {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('overlay');
                }

                toggleBtn.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        sidebar.classList.toggle('show');
                        backdrop.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                    } else {
                        sidebar.classList.toggle('collapsed');
                    }
                });

                if (backdrop) {
                    backdrop.addEventListener('click', () => {
                        sidebar.classList.remove('show');
                        backdrop.style.display = 'none';
                    });
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>