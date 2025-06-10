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
                background-color: var(--menu-color);
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
            }

            #hide-sidebar, #show-sidebar {
                position: absolute;
                bottom: 10px;
                left: 10px;
                background: rgba(0,0,0,0.2);
                color: white;
                border-radius: 5px;
            }

            #show-sidebar {
                position: fixed;
                left: 10px;
                bottom: 10px;
                z-index: 999;
            }
            
            #hide-sidebar:hover, #show-sidebar:hover {
                background: rgba(0,0,0,0.4);
            }

            .submenu {
                list-style: none;
                padding-left: 20px;
                background-color: rgba(0,0,0,0.1);
            }

            .menu-section {
                cursor: pointer;
            }

            .menu-section .fa-angle-down {
                transition: transform 0.3s;
            }

            .menu-section.collapsed .fa-angle-down {
                transform: rotate(-90deg);
            }

            .loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }

            body.dark-theme .loading-overlay {
                background: rgba(0, 0, 0, 0.7);
            }

            @media (max-width: 768px) {
                #sidebar {
                    left: -250px;
                }
                #sidebar.active {
                    left: 0;
                }
                #content {
                    margin-left: 0;
                }
                #menu-toggle {
                    display: block;
                }
            }
            
            .card-header, .card-footer {
                background-color: var(--table-header-bg);
                border-bottom: 1px solid var(--card-border);
            }

            .dropdown-menu {
                background-color: var(--card-bg);
                border-color: var(--card-border);
            }

            .dropdown-item {
                color: var(--text-color);
            }

            .dropdown-item:hover {
                background-color: var(--table-row-hover);
            }
            
            /* Estilo para los badges en modo oscuro */
            .dark-theme .badge {
                color: #e9ecef !important;
                background-color: #495057 !important;
                border: 1px solid #6c757d;
            }
            
            .dark-theme .bg-success { background-color: #28a745 !important; }
            .dark-theme .bg-danger { background-color: #dc3545 !important; }
            .dark-theme .bg-warning { background-color: #ffc107 !important; color: #121212 !important; }
            .dark-theme .bg-info { background-color: #17a2b8 !important; }
            .dark-theme .bg-primary { background-color: var(--btn-primary-bg) !important; }
            
        </style>
    </head>
<body class="dark-theme">
    <div id="app">
        @include('layouts.sidebar')

        <div id="content">
            {{-- @include('layouts.topbar') --}}
            
            <main class="py-4 container-fluid">
                @yield('content')
            </main>
        </div>
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
