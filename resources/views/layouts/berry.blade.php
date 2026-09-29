<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', config('app.name', 'Minimarket'))</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('berry/assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link id="main-font-link" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('berry/assets/fonts/phosphor/duotone/style.css') }}">
    <link rel="stylesheet" href="{{ asset('berry/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('berry/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('berry/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('berry/assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('berry/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('berry/assets/css/style-preset.css') }}">
    @stack('styles')
</head>
<body>
    <div class="loader-bg">
        <div class="loader-track"><div class="loader-fill"></div></div>
    </div>

    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                    <img src="{{ asset('berry/assets/images/logo-dark.svg') }}" alt="Minimarket" class="logo logo-lg">
                </a>
            </div>
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item pc-caption"><label>Menu utama</label><i class="ti ti-dashboard"></i></li>
                    <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    @if (auth()->user()->role === 'admin')
                        <li class="pc-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <a href="{{ route('products.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-package"></i></span>
                                <span class="pc-mtext">Produk</span>
                            </a>
                        </li>
                    @endif
                    <li class="pc-item pc-caption"><label>Akun</label><i class="ti ti-user"></i></li>
                    <li class="pc-item">
                        <a href="{{ route('profile.edit') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user-circle"></i></span>
                            <span class="pc-mtext">Profil saya</span>
                        </a>
                    </li>
                </ul>
                <div class="pc-navbar-card bg-primary rounded">
                    <h4 class="text-white">Minimarket</h4>
                    <p class="text-white opacity-75">Panel pengelolaan toko</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-light text-primary">Pengaturan akun</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="pc-header">
        <div class="header-wrapper">
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <li class="pc-h-item header-mobile-collapse">
                        <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide" aria-label="Sembunyikan menu">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse" aria-label="Buka menu">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="ms-auto">
                <ul class="list-unstyled">
                    <li class="dropdown pc-h-item header-user-profile">
                        <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
                            <span class="user-avtar bg-light-primary text-primary d-inline-flex align-items-center justify-content-center">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                            <span><i class="ti ti-chevron-down"></i></span>
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-header">
                                <h4 class="mb-1">{{ auth()->user()->name }}</h4>
                                <p class="text-muted">{{ auth()->user()->email }}</p>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="ti ti-user"></i><span>Profil saya</span></a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="ti ti-logout"></i><span>Keluar</span></button>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="pc-container">
        <div class="pc-content">
            @yield('content')
        </div>
    </main>

    <footer class="pc-footer">
        <div class="footer-wrapper container-fluid">
            <div class="row">
                <div class="col-sm-6 my-1"><p class="m-0">{{ config('app.name', 'Minimarket') }}</p></div>
                <div class="col-sm-6 ms-auto my-1"><p class="m-0 text-sm-end">Panel administrasi</p></div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('berry/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('berry/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('berry/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('berry/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('berry/assets/js/script.js') }}"></script>
    <script src="{{ asset('berry/assets/js/theme.js') }}"></script>
    <script src="{{ asset('berry/assets/js/plugins/feather.min.js') }}"></script>
    <script>
        document.body.setAttribute('data-pc-theme', 'light');
        font_change('Roboto');
        change_box_container('false');
        layout_caption_change('true');
        layout_rtl_change('false');
        preset_change('preset-1');
    </script>
    @stack('scripts')
</body>
</html>