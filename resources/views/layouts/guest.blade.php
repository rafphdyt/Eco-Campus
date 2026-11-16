<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title') - Eco Campus</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>
        <div class="auth">
            <div class="logo">🌱</div>
            <h1>Eco Campus</h1>
            <p class="sub">Wujudkan aksi nyata Eco-Campus.</p>

            <div class="seg">
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'on' : '' }}">Masuk</a>
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'on' : '' }}">Daftar</a>
            </div>

            @include('partials.flash')
            @yield('content')
        </div>
    </body>
</html>