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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Custom Styles -->
        <style>
            :root {
                --menu-width: 250px;
                --menu-color: #00BFA5;
                
                /* Variables para temas */
                --bg-color: #f8f9fa;
                --text-color: #212529;
                --card-bg: #ffffff;
                --card-border: rgba(0,0,0,0.1);
                --input-bg: #ffffff;
                --input-text: #212529;
                --input-border: #ced4da;
                --table-header-bg: #f8f9fa;
                --table-row-hover: rgba(0,0,0,0.075);
            }
            
            body.dark-theme {
                --bg-color: #121212;
                --text-color: #f0f0f0;
                --card-bg: #1e1e1e;
                --card-border: rgba(255,255,255,0.15);
                --menu-color: #1a7b6f;
                --input-bg: #2c2c2c;
                --input-text: #f0f0f0;
                --input-border: #6c757d;
                --table-header-bg: #2c2c2c;
                --table-row-hover: rgba(255,255,255,0.1);
                --btn-default-bg: #343a40;
                --btn-default-color: #f0f0f0;
                --link-color: #62c4ff;
            }

            body {
                min-height: 100vh;
                margin: 0;
                padding: 0;
                background-color: var(--bg-color);
                color: var(--text-color);
                transition: background-color 0.3s, color 0.3s;
                overflow-x: hidden;
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
                cursor: pointer;
                background: none;
                border: none;
                color: white;
                font-size: 1.2rem;
            }

            .menu-item {
                padding: 15px 20px;
                color: white;
                text-decoration: none;
                display: flex;
                align-items: center;
                transition: 0.2s;
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }

            .menu-item:hover {
                background-color: rgba(255,255,255,0.1);
                color: white;
                text-decoration: none;
            }

            .menu-item.active {
                background-color: rgba(255,255,255,0.2);
            }
            
            .menu-item.highlighted {
                border-left: 3px solid rgba(255, 255, 0, 0.7);
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
                    <button id="theme-toggle" class="theme-toggle">
                        <i class="fas fa-sun"></i>
                    </button>
                </div>
                
                @include('layouts.menu')
            </div>

            <!-- Main content -->
            <div id="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
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

                // Toggle del tema claro/oscuro
                $('#theme-toggle').click(function() {
                    $('body').toggleClass('dark-theme');
                    
                    // Guardar preferencia de tema
                    let theme = $('body').hasClass('dark-theme') ? 'dark' : 'light';
                    localStorage.setItem('theme', theme);
                    
                    // Cambiar icono
                    if (theme === 'dark') {
                    $('#theme-toggle i').removeClass('fa-moon').addClass('fa-sun');
                } else {
                    $('#theme-toggle i').removeClass('fa-sun').addClass('fa-moon');
                    }
                });
                
                // Verificar tema guardado
                let savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                $('body').removeClass('dark-theme');
                $('#theme-toggle i').removeClass('fa-sun').addClass('fa-moon');
            }
            
            // Manejo de submenús
            $('.menu-section').click(function() {
                $(this).toggleClass('collapsed');
                $(this).next('.submenu').toggleClass('show');
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
            });
        </script>
    </body>
    </html>
