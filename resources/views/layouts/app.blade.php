<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Основные настройки -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO -->
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta-description')">
    <meta name="robots" content="@yield('robots')">
    <link rel="canonical" href="@yield('canonical')">
    @yield('pages')

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('meta-description')">
    <meta property="og:url" content="@yield('canonical')">
    <meta property="og:image" content="@yield('link-image')">
    <meta property="og:image:alt" content="@yield('link-image-alt')">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title')">
    <meta name="twitter:description" content="@yield('meta-description')">
    <meta name="twitter:url" content="@yield('canonical')">
    <meta name="twitter:site" content="@AnimePacksOff">

    <!-- Стили и шрифты -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @if(auth()->check() && auth()->user()->hasRole('admin'))
        <!-- код для админа -->
    @endif
    @yield('styles')
    
    <!-- Скрипты аналитики -->

    <script async src="https://analytics.ahrefs.com/analytics.js" data-key="hEqOSlIHOL4rLu6sOW4mYQ"></script>
    
</head>
<body>
    <header id="header">
        <nav class="header-inner">
            <button class="menu-button site-menu-button" data-menu-id="site-menu" aria-label="Open site menu" aria-haspopup="true" aria-expanded="false">
                <div class="lines"></div>
            </button>
            
            <a href="{{ route('home') }}" title="Anime Packs" class="logo">Anime Packs</a>
            
            <ul class="menu site-menu" id="site-menu" aria-label="Site menu">
                <li>
                    <a href="{{ route('home') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
                </li>
                
                <li>
                    <a href="{{ route('catalog') }}" class="{{ request()->is('catalog*') ? 'active' : '' }}">Packs</a>
                </li>
                
                <li>
                    <a href="{{ route('commission') }}" class="{{ request()->is('commission*') ? 'active' : '' }}">Commission</a>
                </li>
            </ul>
            
            <div class="user-action">
                @auth
                    
                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ route('pack-loader') }}">
                            Загрузить новый ресурс пак
                        </a>
                    @endif
                    
                    <button class="menu-button user-menu-button" data-menu-id="user-menu" aria-label="Open user menu" aria-haspopup="true" aria-expanded="false">
                        <img class="user-avatar" src="{{ Auth::user()->avatar }}" alt="User avatar" width="48" height="48" loading="lazy" decoding="async">
                    </button>
                    
                    <ul class="menu user-menu" id="user-menu" aria-label="User menu">
                        <li>
                            <span>
                                <div class="avatar-wrapper">
                                    <img class="user-avatar" src="{{ Auth::user()->avatar }}" alt="User avatar" width="48" height="48" loading="lazy" decoding="async">
                                </div>
                                {{ Auth::user()->name }}
                            </span>
                        </li>
                        
                        <li>
                            <a href="{{ route('logout') }}" aria-label="Log out of account">
                                Log out
                            </a>
                        </li>
                    </ul>
                @endauth
                
                @guest
                    <a href="{{ route('google.login') }}">login</a>
                @endguest
            </div>
        </nav>
    </header>
    
    @yield('ad')
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        <div class="footer-inner">
            <ul class="policies">
                <li>
                    <a href="{{ route('terms-of-use') }}" class="{{ request()->is('terms-of-use*') ? 'active' : '' }}">Terms of use</a>
                </li>
                
                <li>
                    <a href="{{ route('privacy-policy') }}" class="{{ request()->is('privacy-policy*') ? 'active' : '' }}">Privacy policy</a>
                </li>
            </ul>
            
            <p class="copyright">
                &copy; {{ date('Y') }} Andrew Manusha / <a href="https://anime-packs.com">anime-packs.com</a>. All rights reserved.
            </p>
        </div>
    </footer>
    
    <script src="{{ mix('js/app.js') }}" defer></script>

    @yield('scripts')
</body>
</html>