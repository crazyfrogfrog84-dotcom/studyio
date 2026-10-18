<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6f1">
    <title>@yield('title', config('app.name')) · {{ config('app.name') }}</title>
    <script>
        (() => {
            const root = document.documentElement;
            let preference = null;
            try { preference = localStorage.getItem('studyio-theme'); } catch (_) {}
            root.dataset.theme = preference === 'light' || preference === 'dark'
                ? preference
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/studyio-shell.css', 'resources/css/portfolio-themes.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="brand" href="{{ route('home') }}" aria-label="StuDyIo home"><span class="brand-mark">S</span><span>StuDyIo</span></a>
            <nav aria-label="Main navigation">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
                @auth
                    <a href="{{ route('portfolios.index') }}" @class(['active' => request()->routeIs('portfolios.index')])>My portfolios</a>
                    <a class="nav-create" href="{{ route('portfolios.create') }}">Create portfolio <span aria-hidden="true">↗</span></a>
                    <span class="nav-user">{{ auth()->user()->name }}</span>
                    <form class="nav-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
                @else
                    <a href="{{ route('login') }}" @class(['active' => request()->routeIs('login')])>Log in</a>
                    <a class="nav-create" href="{{ route('register') }}">Create account</a>
                @endauth
                @include('components.theme-toggle')
            </nav>
        </div>
    </header>
    <main class="page-shell">
        @if (session('status'))
            <div class="notice" role="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice notice-error" role="alert">Please check the highlighted fields and try again.</div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer"><span>StuDyIo</span><span>Make your work easy to share.</span></footer>
</body>
</html>
