<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <!-- Header -->
    <header class="header">
        <div class="overlay">

            @yield('menu')

            <div class="header-content">
                <h1>Bienvenido a MiTienda</h1>
                <p>Encuentra los mejores productos al mejor precio</p>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    @yield('contenido')

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif

    <!-- Footer -->
    @yield('footer')

    @stack('modals')

    @livewireScripts
</body>

</html>
