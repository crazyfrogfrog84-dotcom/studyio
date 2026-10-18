<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f6f1">
    <title>@yield('title', config('app.name')) · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="nav-wrap">
            <a class="brand" href="{{ route('home') }}" aria-label="StuDyIo home"><span class="brand-mark">S</span><span>StuDyIo</span></a>
            <nav aria-label="Main navigation">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
                <a href="{{ route('portfolios.index') }}" @class(['active' => request()->routeIs('portfolios.index')])>My portfolios</a>
                <a class="nav-create" href="{{ route('portfolios.create') }}">Create portfolio <span aria-hidden="true">↗</span></a>
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
