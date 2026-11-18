<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title') - Admin Eco Campus</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>
        @php $user = auth()->user(); @endphp

        <div class="adm">
            <aside class="side">
                <div class="side-brand">
                    <div class="logo sm">🌱</div>
                    <strong>Eco Campus<br>Admin</strong>
                </div>

                <nav>
                    <a class="side-btn {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Ringkasan</a>
                    <a class="side-btn {{ request()->routeIs('admin.challenges') ? 'active' : '' }}" href="{{ route('admin.challenges') }}">Tantangan</a>
                    <a class="side-btn {{ request()->routeIs('admin.articles') ? 'active' : '' }}" href="{{ route('admin.articles') }}">Artikel</a>
                    <a class="side-btn {{ request()->routeIs('admin.proofs') ? 'active' : '' }}" href="{{ route('admin.proofs') }}">Bukti</a>
                </nav>

                <div class="side-user">
                    @include('partials.avatar', ['u' => $user])
                    <div class="who"><strong>{{ $user->name }}</strong><span>{{ $user->email }}</span></div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn danger" style="width:100%">Keluar</button>
                </form>
            </aside>

            <section class="adm-main">
                @include('partials.flash')
                <h1 class="adm-title">@yield('title')</h1>
                <main>@yield('content')</main>
            </section>
        </div>
    </body>
</html>