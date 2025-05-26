<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GrowLocal') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Optional: Bootstrap JS (for alerts, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

        <div id="app" class="d-flex">
        {{-- Sidebar for grower role --}}
        @auth
            @if(auth()->user()->hasRole('grower'))
                @include('layouts.sidebar') {{-- create this partial --}}
            @endif
        @endauth

        <div class="flex-fill">
            @include('layouts.navigation') {{-- Top nav --}}

            <!-- Page Content -->
            <main class="py-4 px-3">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>