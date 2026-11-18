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
        @php $user = auth()->user(); @endphp

        <div class="app">
            <header class="topbar">
                @include('partials.avatar', ['u' => $user])
                <div class="who">
                    <strong>{{ $user->name }}</strong>
                    <span>{{ $user->npm }} · {{ $user->department->name }}</span>
                </div>
                <div class="points"><b>{{ $user->total_points }}</b><small>poin</small></div>
            </header>

            @include('partials.flash')

            <main>@yield('content')</main>

            <nav class="bottom-nav">
                <a class="nav-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-btn {{ request()->routeIs('library') ? 'active' : '' }}" href="{{ route('library') }}">E-Library</a>
                <a class="nav-btn {{ request()->routeIs('challenges') ? 'active' : '' }}" href="{{ route('challenges') }}">Challenge</a>
                <a class="nav-btn {{ request()->routeIs('account') ? 'active' : '' }}" href="{{ route('account') }}">Account</a>
            </nav>
        </div>
    </body>
</html>