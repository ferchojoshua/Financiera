    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Prestamos') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/function-selector.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dark-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('css/function-selector.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @stack('styles')
    
    <!-- Custom Styles -->
        <style>
            :root {
                --menu-width: 250px;
                --menu-color: #00BFA5;
                --menu-hover: #009688;
                --menu-active: #00796B;
                --menu-text: #FFFFFF;
                
                /* Variables para temas */
                --bg-color: #f8f9fa;
                --text-color: #212529;
                --card-bg: #ffffff;
                --card-border: rgba(0,0,0,0.1);
                --input-bg: #ffffff;
                --input-text: #212529;
                --input-border: #ced4da;
                --input-focus-border: #10775c;
                --input-focus-rgb: 16, 119, 92; /* Para el box-shadow con opacidad */
                --table-header-bg: #f0f5f3;
                --table-row-hover: rgba(16,119,92,0.075);
                --btn-primary-bg: #10775c;
                --btn-primary-color: #ffffff;
                --btn-secondary-bg: #6c757d;
                --btn-secondary-color: #ffffff;
                --link-color: #0e7e5c;
                --link-hover-color: #095e45;
                --badge-text: #fff;
                --success-color: #28a745;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #17a2b8;

                /* Variables para alertas - Tema claro */
                --success-color-bg: #d4edda;
                --success-color-border: #c3e6cb;
                --success-color-text: #155724;
                --danger-color-bg: #f8d7da;
                --danger-color-border: #f5c6cb;
                --danger-color-text: #721c24;
                --warning-color-bg: #fff3cd;
                --warning-color-border: #ffeeba;
                --warning-color-text: #856404;
                --info-color-bg: #d1ecf1;
                --info-color-border: #bee5eb;
                --info-color-text: #0c5460;
            }
            
            body.dark-theme {
                --bg-color: #121212;
                --text-color: #f0f0f0;
                --card-bg: #1e1e1e;
                --card-border: rgba(255,255,255,0.15);
                --menu-color: #1a7b6f;
                --menu-hover: #145e55;
                --menu-active: #0d4741;
                --input-bg: #2c2c2c;
                --input-text: #f0f0f0;
                --input-border: #555555;
                --input-focus-border: #15967a;
                --input-focus-rgb: 21, 150, 122; /* Para el box-shadow con opacidad */
                --table-header-bg: rgba(16,119,92,0.25);
                --table-row-hover: rgba(255,255,255,0.1);
                --btn-default-bg: #343a40;
                --btn-default-color: #f0f0f0;
                --link-color: #4db6ac;
                --link-hover-color: #80cbc4;
                --badge-text: #fff;
                --success-color: #5cb85c;
                --danger-color: #ff5252;
                --warning-color: #ffd740;
                --info-color: #40c4ff;
                --text-muted: #aaaaaa;

                /* Variables para alertas - Tema oscuro */
                --success-color-bg: #1a3c23;
                --success-color-border: #285734;
                --success-color-text: #a8d5b5;
                --danger-color-bg: #4d1c20;
                --danger-color-border: #72292f;
                --danger-color-text: #f5c6cb;
                --warning-color-bg: #524415;
                --warning-color-border: #796621;
                --warning-color-text: #ffeeba;
                --info-color-bg: #173b43;
                --info-color-border: #225a68;
                --info-color-text: #bee5eb;
            }

            body {
                min-height: 100vh;
                margin: 0;
                padding: 0;
                background-color: var(--bg-color);
                color: var(--text-color);
                transition: background-color 0.3s, color 0.3s;
                overflow-x: hidden;
                font-family: 'Roboto', 'Nunito', sans-serif;
                font-size: 14px;
                line-height: 1.6;
            }

            #app {
                display: flex;
                min-height: 100vh;
                position: relative;
            }

            #sidebar {
                width: var(--menu-width);
                min-height: 100vh;
                position: fixed;
                left: 0;
                top: 0;
                background-color: var(--menu-color);
                transition: 0.3s;
                z-index: 1000;
                overflow-y: auto;
                max-height: 100vh;
                scrollbar-width: thin;
                scrollbar-color: rgba(255,255,255,0.2) transparent;
                padding-bottom: 60px;
                box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            }

            /* Estilo para scrollbar personalizado en navegadores WebKit (Chrome, Safari) */
            #sidebar::-webkit-scrollbar {
                width: 6px;
            }
            
            #sidebar::-webkit-scrollbar-track {
                background: transparent;
            }
            
            #sidebar::-webkit-scrollbar-thumb {
                background-color: rgba(255,255,255,0.2);
                border-radius: 3px;
            }
            
            #sidebar::-webkit-scrollbar-thumb:hover {
                background-color: rgba(255,255,255,0.3);
            }

            #content {
                margin-left: var(--menu-width);
                padding: 20px;
                flex: 1;
                transition: 0.3s;
                min-height: 100vh;
                background-color: var(--bg-color);
            }

            .user-info {
                padding: 20px;
                color: white;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                background-color: rgba(0,0,0,0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .theme-toggle {
                background: transparent;
                color: rgba(255,255,255,0.9);
                border: none;
                padding: 8px;
                cursor: pointer;
                transition: all 0.3s;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .theme-toggle:hover {
                color: white;
                background-color: rgba(255,255,255,0.2);
            }
            
            .theme-toggle:focus {
                outline: none;
                box-shadow: 0 0 0 2px rgba(255,255,255,0.5);
            }

            .menu-item {
                padding: 15px 20px;
                color: var(--menu-text);
                text-decoration: none;
                display: flex;
                align-items: center;
                transition: 0.2s;
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }

            .menu-item:hover {
                background-color: var(--menu-hover);
                color: white;
                text-decoration: none;
            }

            .menu-item.active {
                background-color: var(--menu-active);
                border-left: 4px solid #FFEB3B;
                font-weight: bold;
            }

            .menu-item i {
                margin-right: 10px;
                width: 20px;
                text-align: center;
            }

            #menu-toggle {
                display: none;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1001;
                background: var(--menu-color);
                border: none;
                color: white;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }

            @media (max-width: 768px) {
                #sidebar {
                    left: calc(var(--menu-width) * -1);
                    z-index: 1050;
                    width: 85%;
                    max-width: 320px;
                    box-shadow: 0 0 15px rgba(0,0,0,0.2);
                }

                #content {
                    margin-left: 0;
                    width: 100%;
                    padding: 15px;
                }

                #menu-toggle {
                    display: block;
                    z-index: 1060;
                }

                #sidebar.active {
                    left: 0;
                    padding-bottom: 80px;
                }

                .card {
                    overflow-x: auto;
                }
                
                .table-responsive {
                    overflow-x: auto;
                }
                
                body.menu-open {
                    overflow: hidden;
                }
                
                body.menu-open #sidebar {
                    overflow-y: auto;
                    height: 100vh;
                }
            }

            .card {
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 10px;
                border: none;
                margin-bottom: 20px;
                background-color: var(--card-bg);
                color: var(--text-color);
                transition: background-color 0.3s, color 0.3s;
            }

            .card-header {
                border-top-left-radius: 10px !important;
                border-top-right-radius: 10px !important;
                font-weight: bold;
                padding: 12px 20px;
            }

            .card-body {
                padding: 20px;
            }

            /* Estilos para formularios */
            .form-control, .form-select {
                border-radius: 6px;
                padding: 10px 12px;
                border: 1px solid var(--input-border);
                background-color: var(--input-bg);
                color: var(--input-text);
                transition: all 0.3s;
                height: auto;
            }

            .form-control:focus, .form-select:focus {
                border-color: var(--menu-color);
                box-shadow: 0 0 0 0.2rem rgba(0, 191, 165, 0.25);
            }

            label {
                font-weight: 600;
                margin-bottom: 8px;
                color: var(--text-color);
            }

            .btn {
                border-radius: 6px;
                padding: 10px 20px;
                font-weight: 600;
                transition: all 0.3s;
            }

            .btn-primary {
                background-color: var(--btn-primary-bg);
                border-color: var(--btn-primary-bg);
                color: var(--btn-primary-color);
            }

            .btn-primary:hover {
                background-color: #0069d9;
                border-color: #0062cc;
            }

            .btn-secondary {
                background-color: var(--btn-secondary-bg);
                border-color: var(--btn-secondary-bg);
                color: var(--btn-secondary-color);
            }

            .btn-secondary:hover {
                background-color: #5a6268;
                border-color: #545b62;
            }

            /* Colores y fondos */
            .bg-success { background-color: #28a745 !important; }
            .bg-warning { background-color: #ffc107 !important; }
            .bg-danger { background-color: #dc3545 !important; }
            .bg-info { background-color: #17a2b8 !important; }
            .bg-primary { background-color: #007bff !important; }
            
            /* Dark theme adaptations */
            body.dark-theme .card {
                box-shadow: 0 0 15px rgba(0,0,0,0.3);
            }
            
            body.dark-theme .table {
                color: var(--text-color);
            }
                
            body.dark-theme .table thead th {
                background-color: var(--table-header-bg);
                color: var(--text-color);
            }
                
            /* Estilos para menús desplegables */
            .menu-section {
                color: white;
                display: block;
                position: relative;
                padding: 15px 20px;
                cursor: pointer;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                font-weight: bold;
                transition: background-color 0.3s;
            }
            
            .menu-section:hover {
                background-color: rgba(255,255,255,0.1);
            }
            
            .menu-section i.toggle-icon {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                transition: all 0.3s;
            }
            
            .menu-section.collapsed i.toggle-icon {
                transform: translateY(-50%) rotate(-90deg);
            }
            
            .submenu {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }
            
            .submenu.show {
                max-height: 1000px;
            }
            
            .submenu .menu-item {
                padding-left: 40px;
            }
            
            /* Colores de sección para el menú */
            .menu-section.configuracion { background-color: rgba(0, 150, 136, 0.8); }
            .menu-section.clientes { background-color: rgba(0, 137, 123, 0.8); }
            .menu-section.pymes { background-color: rgba(0, 121, 107, 0.8); }
            .menu-section.reportes { background-color: rgba(0, 105, 92, 0.8); }
            .menu-section.cobranza { background-color: rgba(0, 77, 64, 0.8); }
            .menu-section.seguridad { background-color: rgba(0, 150, 136, 0.8); }
            .menu-section.rutas { background-color: rgba(0, 137, 123, 0.8); }
            .menu-section.caja { background-color: rgba(0, 121, 107, 0.8); }

            /* Estilos para tablas */
            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: var(--text-color);
                border-collapse: separate;
                border-spacing: 0;
            }

            .table th, .table td {
                padding: 12px 15px;
                vertical-align: middle;
                border-top: 1px solid var(--card-border);
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid var(--card-border);
                background-color: var(--table-header-bg);
                color: var(--text-color);
                font-weight: 600;
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(0, 0, 0, 0.05);
            }

            .table-hover tbody tr:hover {
                background-color: var(--table-row-hover);
            }

            /* Estilos para alertas */
            .alert {
                border-radius: 8px;
                padding: 15px 20px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }
            
            .alert-success {
                color: #155724;
                background-color: #d4edda;
                border-color: #c3e6cb;
            }
            
            .alert-danger {
                color: #721c24;
                background-color: #f8d7da;
                border-color: #f5c6cb;
            }
            
            .alert-warning {
                color: #856404;
                background-color: #fff3cd;
                border-color: #ffeeba;
            }
            
            .alert-info {
                color: #0c5460;
                background-color: #d1ecf1;
                border-color: #bee5eb;
            }

            /* Estilos para el indicador de carga */
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            
            .spinner-border {
                width: 3rem;
                height: 3rem;
            }

            /* Estilos para tema oscuro */
            body.dark-theme {
                --bg-color: #121212;
                --text-color: #f0f0f0;
                --card-bg: #1e1e1e;
                --card-border: rgba(255,255,255,0.15);
                --menu-color: #1a7b6f;
                --menu-hover: #145e55;
                --menu-active: #0d4741;
                --input-bg: #2c2c2c;
                --input-text: #f0f0f0;
                --input-border: #555555;
                --input-focus-border: #15967a;
                --table-header-bg: rgba(16,119,92,0.25);
                --table-row-hover: rgba(255,255,255,0.1);
                --btn-default-bg: #343a40;
                --btn-default-color: #f0f0f0;
                --link-color: #4db6ac;
                --link-hover-color: #80cbc4;
                --badge-text: #fff;
                --success-color: #5cb85c;
                --danger-color: #ff5252;
                --warning-color: #ffd740;
                --info-color: #40c4ff;
                --text-muted: #aaaaaa;
            }
            
            /* Ajustes para badges en tema oscuro */
            body.dark-theme .badge {
                color: #ffffff !important;
            }
            
            body.dark-theme .badge.bg-primary {
                background-color: #0d6efd !important;
            }
            
            body.dark-theme .badge.bg-success {
                background-color: #198754 !important;
            }
            
            body.dark-theme .badge.bg-danger {
                background-color: #dc3545 !important;
            }
            
            body.dark-theme .badge.bg-warning {
                background-color: #ffc107 !important;
                color: #212529 !important;
            }
            
            body.dark-theme .badge.bg-info {
                background-color: #0dcaf0 !important;
                color: #212529 !important;
            }
            
            body.dark-theme .list-group-item {
                background-color: var(--card-bg);
                color: var(--text-color);
                border-color: var(--card-border);
            }

            /* Estilos para íconos en cards */
            .card-header i {
                margin-right: 8px;
                color: var(--primary-color);
            }
            
            /* Estilos para menú lateral oculto/visible */
            #sidebar.hidden {
                left: calc(var(--menu-width) * -1);
            }
            
            #content.full-width {
                margin-left: 0;
                width: 100%;
            }
            
            .show-sidebar-btn {
                position: fixed;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                background-color: var(--menu-color);
                color: white;
                border: none;
                border-radius: 0 4px 4px 0;
                padding: 10px 5px;
                z-index: 900;
                cursor: pointer;
                box-shadow: 2px 0 5px rgba(0,0,0,0.2);
                transition: background-color 0.3s;
            }
            
            .show-sidebar-btn:hover {
                background-color: var(--primary-color);
            }
            
            .hidden {
                display: none !important;
            }
            
            .user-actions {
                display: flex;
                align-items: center;
            }
            
            .hide-sidebar-btn {
                background: transparent;
                color: rgba(255,255,255,0.7);
                border: none;
                padding: 3px 6px;
                margin-right: 8px;
                cursor: pointer;
                transition: color 0.3s;
            }
            
            .hide-sidebar-btn:hover {
                color: white;
            }
            
            .theme-toggle {
                background: transparent;
                color: rgba(255,255,255,0.9);
                border: none;
                padding: 8px;
                cursor: pointer;
                transition: all 0.3s;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .theme-toggle:hover {
                color: white;
                background-color: rgba(255,255,255,0.2);
            }
            
            .theme-toggle:focus {
                outline: none;
                box-shadow: 0 0 0 2px rgba(255,255,255,0.5);
            }
            
            /* Mejorar legibilidad en menú lateral */
            .sidebar .menu-item, 
            .sidebar .user-info,
            .sidebar .menu-section {
                font-size: 0.95rem;
                font-weight: 500;
                letter-spacing: 0.3px;
            }
            
            .sidebar .user-info strong {
                font-size: 1.05rem;
                font-weight: 600;
            }
            
            .sidebar .user-info small {
                font-size: 0.85rem;
                opacity: 0.9;
            }

            /* Estilos para el icono monetario en sección de cobrador */
            .menu-section.cobrador i.fas.fa-money-bill {
                color: #28a745;
            }
            
            /* Estilo para íconos de cobrador */
            .submenu a i.fas.fa-hand-holding-usd {
                color: #28a745;
            }
        </style>
    </head>
<body class="dark-theme">
        <div id="app">
        @guest
            <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Prestamos') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">

                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>
        @else
            <!-- Menu toggle button for mobile -->
            <button id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Sidebar menu -->
            <div id="sidebar">
                <div class="user-info">
                    <div>
                        <strong>{{ Auth::user()->name }}</strong><br>
                        <small>{{ ucfirst(Auth::user()->role ?? 'Usuario') }}</small>
                    </div>
                    <div class="user-actions">
                        <button id="hide-sidebar" class="hide-sidebar-btn" title="Ocultar menú">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button id="theme-toggle" class="theme-toggle" title="Cambiar tema">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
                
                @include('layouts.menu')
            </div>

            <!-- Botón para mostrar el menú cuando está oculto -->
            <button id="show-sidebar" class="show-sidebar-btn hidden">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Main content -->
            <div id="content">
                <!-- Mensajes Flash -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> {{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                @yield('admin-section')
                @yield('content')
            </div>
        @endguest
        </div>

    <!-- Scripts adicionales -->
        <script>
            $(document).ready(function() {
                // Toggle del menú en móvil
                $('#menu-toggle').click(function() {
                    $('#sidebar').toggleClass('active');
                    $('body').toggleClass('menu-open');
                });

                // Ocultar menú lateral
                $('#hide-sidebar').click(function() {
                    $('#sidebar').addClass('hidden');
                    $('#content').addClass('full-width');
                    $('#show-sidebar').removeClass('hidden');
                    localStorage.setItem('sidebar-visible', 'false');
                });
                
                // Mostrar menú lateral
                $('#show-sidebar').click(function() {
                    $('#sidebar').removeClass('hidden');
                    $('#content').removeClass('full-width');
                    $('#show-sidebar').addClass('hidden');
                    localStorage.setItem('sidebar-visible', 'true');
                });
                
                // Verificar estado guardado del sidebar
                let sidebarVisible = localStorage.getItem('sidebar-visible');
                if (sidebarVisible === 'false') {
                    $('#sidebar').addClass('hidden');
                    $('#content').addClass('full-width');
                    $('#show-sidebar').removeClass('hidden');
                }

                // Toggle del tema claro/oscuro
                $('#theme-toggle').click(function() {
                    $('body').toggleClass('dark-theme');
                    
                    // Guardar preferencia de tema
                    let theme = $('body').hasClass('dark-theme') ? 'dark' : 'light';
                    localStorage.setItem('theme', theme);
                    
                    // Cambiar icono
                    if (theme === 'dark') {
                        $('#theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
                        // Forzar actualización de colores de badges en modo oscuro
                        $('.badge').each(function() {
                            $(this).addClass('dark-mode-badge');
                        });
                    } else {
                        $('#theme-toggle i').removeClass('fa-sun').addClass('fa-moon');
                        // Remover clase específica de tema oscuro
                        $('.badge').each(function() {
                            $(this).removeClass('dark-mode-badge');
                        });
                    }
                });
                
                // Verificar tema guardado
                let savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'light') {
                    $('body').removeClass('dark-theme');
                    $('#theme-toggle i').removeClass('fa-sun').addClass('fa-moon');
                    $('.badge').removeClass('dark-mode-badge');
                } else if (savedTheme === 'dark') {
                    $('body').addClass('dark-theme');
                    $('#theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
                    $('.badge').addClass('dark-mode-badge');
                }
                
                // Manejo de submenús - Prevenir cierre al hacer clic
                $('.menu-section').click(function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).toggleClass('collapsed');
                    $(this).next('.submenu').toggleClass('show');
                });
                
                // Navegación SPA para evitar recargas de página completa
                $(document).on('click', '[data-link]', function(e) {
                    e.preventDefault();
                    
                    const url = $(this).attr('href');
                    
                    // Cerrar menú en móviles al seleccionar una opción
                    if (window.innerWidth <= 768) {
                        $('#sidebar').removeClass('active');
                        $('body').removeClass('menu-open');
                    }
                    
                    // Mostrar indicador de carga
                    $('#content').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
                    
                    // Agregar clase active y quitar de los demás
                    $('.menu-item').removeClass('active');
                    $(this).addClass('active');
                    
                    // Cargar contenido mediante AJAX
                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            // Actualizar URL del navegador sin recargar
                            history.pushState({}, '', url);
                            
                            // Extraer el contenido principal
                            let content = $(response).find('#content').html();
                            if (content) {
                                $('#content').html(content);
                            } else {
                                // Si no se puede extraer correctamente, recargar la página
                                window.location.href = url;
                                return;
                            }
                            
                            // Ejecutar scripts en el contenido cargado
                            $('#content script').each(function() {
                                eval($(this).text());
                            });
                            
                            // Quitar overlay de carga
                            $('.loading-overlay').remove();
                        },
                        error: function() {
                            // En caso de error, redirigir normalmente
                            window.location.href = url;
                        }
                    });
                });
                
                // Manejar la navegación del botón atrás/adelante
                $(window).on('popstate', function() {
                    location.reload();
                });
                
                // Detectar URL actual y mostrar el submenú correspondiente
                const currentPath = window.location.pathname;
                $('.menu-item').each(function() {
                    const href = $(this).attr('href');
                    if (href && currentPath.includes(href) && !href.endsWith('home')) {
                        $(this).addClass('active');
                        $(this).closest('.submenu').addClass('show');
                        $(this).closest('.submenu').prev('.menu-section').removeClass('collapsed');
                    }
                });
                
                // Asegurar que alertas se cierran automáticamente
                setTimeout(function() {
                    $('.alert').fadeOut('slow');
                }, 5000);
            });
        </script>
        @stack('scripts')
    </body>
</html>
