<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Sistema de Préstamos'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/material-dashboard.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-color="purple" data-background-color="black">
            <div class="logo">
                <a href="{{ url('/home') }}" class="simple-text logo-normal">
                    {{ config('app.name', 'Sistema de Préstamos') }}
                </a>
            </div>
            <div class="sidebar-wrapper">
                <ul class="nav">
                    <li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/home') }}">
                            <i class="fas fa-home"></i>
                            <p>INICIO</p>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ Request::is('clients*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('clients.index') }}">
                            <i class="fas fa-users"></i>
                            <p>CLIENTES</p>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ Request::is('credit*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('credit.index') }}">
                            <i class="fas fa-dollar-sign"></i>
                            <p>CRÉDITOS</p>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ Request::is('routes*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('routes.index') }}">
                            <i class="fas fa-map-marked-alt"></i>
                            <p>RUTAS</p>
                        </a>
                    </li>
                    
                    <li class="nav-item {{ Request::is('config*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('config.index') }}">
                            <i class="fas fa-cogs"></i>
                            <p>CONFIGURACIÓN</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Panel -->
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
                <div class="container-fluid">
                    <div class="navbar-wrapper">
                        <a class="navbar-brand" href="#">@yield('title', 'Panel de Control')</a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name ?? 'Usuario' }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar Sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            
            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright float-right">
                        &copy; {{ date('Y') }} Sistema de Préstamos
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/material-dashboard.min.js') }}"></script>
</body>
</html> 