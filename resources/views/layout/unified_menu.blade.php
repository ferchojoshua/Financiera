@if(Auth::user())
<div class="sidebar-menu">
    <div class="logo">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
    </div>

    <div class="user-info">
        <div class="user-name">{{ Auth::user()->name }}</div>
        <div class="user-role">{{ ucfirst(Auth::user()->role ?? Auth::user()->level) }}</div>
    </div>
    
    <nav class="nav-menu">
        <a href="{{ url('/home') }}" class="nav-item">
            <i class="fa fa-home"></i>
            <span>INICIO</span>
        </a>

        @php
            $user = Auth::user();
            $showAdminMenu = false;
            
            // Verificar primero bypass de permisos
            if (method_exists($user, 'shouldBypassPermissionChecks') && $user->shouldBypassPermissionChecks()) {
                $showAdminMenu = true;
            } 
            // Verificar si es admin o superadmin
            else if ((method_exists($user, 'isAdmin') && $user->isAdmin()) || 
                     (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) ||
                     $user->level == 'admin' || 
                     $user->role == 'superadmin' || 
                     $user->role == 'admin') {
                $showAdminMenu = true;
            }
            // Verificar acceso a módulos específicos como última opción
            else if (method_exists($user, 'hasModuleAccess') && 
                    ($user->hasModuleAccess('admin') || 
                     $user->hasModuleAccess('dashboard') || 
                     $user->hasModuleAccess('usuarios'))) {
                $showAdminMenu = true;
            }
        @endphp

        <!-- Menú para administradores -->
        @if($showAdminMenu)
            <div class="menu-section">
                <a href="{{ url('admin/user/create') }}" class="nav-item">
                    <i class="fa fa-user-plus"></i>
                    <span>CREAR USUARIO</span>
                </a>

                <a href="{{ url('admin/wallet/create') }}" class="nav-item">
                    <i class="fa fa-briefcase"></i>
                    <span>CREAR CARTERA</span>
                </a>

                <a href="{{ url('admin/user') }}" class="nav-item">
                    <i class="fa fa-users"></i>
                    <span>USUARIOS</span>
                </a>

                <a href="{{ url('admin/wallet') }}" class="nav-item">
                    <i class="fa fa-list"></i>
                    <span>CARTERAS</span>
                </a>

                <a href="{{ url('admin/route') }}" class="nav-item">
                    <i class="fa fa-road"></i>
                    <span>RUTAS</span>
                </a>

                <a href="{{ url('admin/statistics') }}" class="nav-item">
                    <i class="fa fa-bar-chart"></i>
                    <span>ESTADÍSTICAS</span>
                </a>

                <a href="{{ url('admin/summary-wallet') }}" class="nav-item">
                    <i class="fa fa-money"></i>
                    <span>RESUMEN CARTERAS</span>
                </a>

                <a href="{{ url('admin/summary-supervisor') }}" class="nav-item">
                    <i class="fa fa-eye"></i>
                    <span>RESUMEN SUPERVISOR</span>
                </a>

                <a href="{{ url('admin/tracker') }}" class="nav-item">
                    <i class="fa fa-map-marker"></i>
                    <span>RASTREO</span>
                </a>
                
                <a href="{{ url('config') }}" class="nav-item">
                    <i class="fa fa-gear"></i>
                    <span>CONFIGURACIÓN</span>
                </a>
                
                <a href="{{ url('pymes') }}" class="nav-item">
                    <i class="fa fa-building"></i>
                    <span>PYMES</span>
                </a>
                
                <a href="{{ url('garantias') }}" class="nav-item">
                    <i class="fa fa-shield"></i>
                    <span>GARANTÍAS</span>
                </a>
                
                <a href="{{ url('creditos') }}" class="nav-item">
                    <i class="fa fa-money"></i>
                    <span>CRÉDITOS</span>
                </a>
                
                <a href="{{ url('pagos') }}" class="nav-item">
                    <i class="fa fa-credit-card"></i>
                    <span>PAGOS</span>
                </a>
                
                <a href="{{ url('cobranzas') }}" class="nav-item">
                    <i class="fa fa-dollar"></i>
                    <span>COBRANZAS</span>
                </a>
                
                <a href="{{ url('contabilidad') }}" class="nav-item">
                    <i class="fa fa-book"></i>
                    <span>CONTABILIDAD</span>
                </a>
                
                <a href="{{ url('reports') }}" class="nav-item">
                    <i class="fa fa-bar-chart"></i>
                    <span>REPORTES</span>
                </a>
            </div>
        @endif

        @if(Auth::user()->level == 'supervisor' || Auth::user()->role == 'supervisor')
            <div class="menu-section">
                <a href="{{ url('supervisor/agent') }}" class="nav-item">
                    <i class="fa fa-money"></i>
                    <span>ASIGNAR BASE</span>
                </a>

                <a href="{{ url('supervisor/close') }}" class="nav-item">
                    <i class="fa fa-lock"></i>
                    <span>CIERRE DIARIO</span>
                </a>

                <a href="{{ url('supervisor/client') }}" class="nav-item">
                    <i class="fa fa-edit"></i>
                    <span>EDICIÓN CLIENTE</span>
                </a>

                <a href="{{ url('supervisor/tracker') }}" class="nav-item">
                    <i class="fa fa-search"></i>
                    <span>RASTRO AGENTE</span>
                </a>

                <a href="{{ url('supervisor/review') }}" class="nav-item">
                    <i class="fa fa-eye"></i>
                    <span>REVISIÓN CARTERA</span>
                </a>

                <a href="{{ url('supervisor/statistics') }}" class="nav-item">
                    <i class="fa fa-bar-chart"></i>
                    <span>ESTADÍSTICA</span>
                </a>

                <a href="{{ url('supervisor/cash') }}" class="nav-item">
                    <i class="fa fa-money"></i>
                    <span>CAJA</span>
                </a>

                <a href="{{ url('supervisor/bill') }}" class="nav-item">
                    <i class="fa fa-shopping-cart"></i>
                    <span>GASTOS</span>
                </a>
            </div>
        @endif

        @if(Auth::user()->level == 'agent' || Auth::user()->role == 'colector')
            <div class="menu-section">
                <a href="{{ url('client/create') }}" class="nav-item">
                    <i class="fa fa-user-plus"></i>
                    <span>CREAR CLIENTE</span>
                </a>

                <a href="{{ url('route') }}" class="nav-item">
                    <i class="fa fa-road"></i>
                    <span>RUTA</span>
                </a>

                <a href="{{ url('history') }}" class="nav-item">
                    <i class="fa fa-history"></i>
                    <span>HISTORIAL</span>
                </a>

                <a href="{{ url('simulator') }}" class="nav-item">
                    <i class="fa fa-calculator"></i>
                    <span>SIMULADOR</span>
                </a>

                <a href="{{ url('payment') }}" class="nav-item">
                    <i class="fa fa-money"></i>
                    <span>PAGOS</span>
                </a>

                <a href="{{ url('review-route') }}" class="nav-item">
                    <i class="fa fa-eye"></i>
                    <span>REVISAR RUTA</span>
                </a>
            </div>
        @endif

        <a href="{{ route('logout') }}" class="nav-item"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out"></i>
            <span>CERRAR SESIÓN</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </nav>
</div>
@endif