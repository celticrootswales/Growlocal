<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GrowLocal') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Vite styles (main build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap (you already use it) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="d-flex" style="font-family: 'Inter', system-ui, sans-serif;">
    @auth
        @include('layouts.sidebar')
    @endauth

    <div class="flex-grow-1 d-flex flex-column">
        @include('layouts.topnav')

        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>
</body>
</html>