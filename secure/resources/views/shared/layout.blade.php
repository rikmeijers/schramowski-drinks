<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#ffffff">
    <meta name="author" content="{{ config('app.name', 'App') }}">

    @yield('customSeoTags')

    <link rel="manifest" href="{{ url('/assets/manifest.json') }}">

    <link rel="shortcut icon" href="{{ url('favicon.ico') }}">
    <link rel="icon" href="{{ url('/assets/images/branding/icon.png') }}">
    <link rel="apple-touch-icon" href="{{ url('/assets/images/branding/icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/assets/css/core/root.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/forms.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/cookies.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/header.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/core/ui.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/global.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @yield('customStyles')

    <title>
        {{ config('app.name', 'App') }} - Getränke Reservierungen{{ isset($title) ? ' | ' . $title : '' }}
    </title>
</head>
@include('cookie-consent::index')
<body>
    @if ($header ?? true)
        <header>
            @include('shared.header')
        </header>
    @endif

    <main class="container mt-4">
        @yield('content')
    </main>

    @if ($footer ?? true)
        <footer class="mt-4">
            @include('shared.footer')
        </footer>
    @endif

    <script src="{{ url('/assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ url('/assets/js/core/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('/assets/js/core/cookies.min.js') }}"></script>
    <script src="{{ url('/assets/js/global.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ url('/assets/js/flatpickr-global.js') }}"></script>

    @yield('customScripts')
</body>
</html>
