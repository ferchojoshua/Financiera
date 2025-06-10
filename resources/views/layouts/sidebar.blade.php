<div id="sidebar">
    <div class="user-info">
        <div>
            <strong>{{ Auth::user()->name }}</strong><br>
            <small>{{ Auth::user()->getRoleName() }}</small>
        </div>
        <button id="theme-toggle" class="btn btn-sm" title="Cambiar tema">
            <i class="fas fa-sun"></i>
        </button>
    </div>

    <!-- INICIO -->
    <a href="{{ route('home') }}" class="menu-item {{ request()->is('/') || request()->is('home') ? 'active' : '' }}" data-link>
        <i class="fas fa-home"></i>
        <span>INICIO</span>
    </a>

    <!-- CLIENTES -->
    <div class="menu-section menu-item {{ request()->is('client*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#clientMenu">
        <i class="fas fa-users"></i>
        <span>CLIENTES</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('client*') ? 'show' : '' }}" id="clientMenu">
        <a href="{{ route('client.index') }}" class="menu-item" data-link>Lista de Clientes</a>
        <a href="{{ route('client.create') }}" class="menu-item" data-link>Nuevo Cliente</a>
        {{-- <a href="{{ route('client.types') }}" class="menu-item" data-link>Tipos de Cliente</a> --}}
    </div>

    <!-- RUTAS -->
    <div class="menu-section menu-item {{ request()->is('routes*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#routesMenu">
        <i class="fas fa-map-marked-alt"></i>
        <span>RUTAS</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('routes*') ? 'show' : '' }}" id="routesMenu">
        <a href="{{ route('routes.index') }}" class="menu-item" data-link>Lista de Rutas</a>
        <a href="{{ route('routes.create') }}" class="menu-item" data-link>Nueva Ruta</a>
        <a href="{{ route('ubicaciones.index') }}" class="menu-item" data-link>Mapa de Rutas</a>
    </div>

    <!-- SUCURSALES -->
    <div class="menu-section menu-item {{ request()->is('branches*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#branchesMenu">
        <i class="fas fa-building"></i>
        <span>SUCURSALES</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('branches*') ? 'show' : '' }}" id="branchesMenu">
        <a href="{{ route('branches.index') }}" class="menu-item" data-link>Lista de Sucursales</a>
        <a href="{{ route('branches.create') }}" class="menu-item" data-link>Nueva Sucursal</a>
    </div>

    <!-- CRÉDITOS -->
    <div class="menu-section menu-item {{ request()->is('credit*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#creditMenu">
        <i class="fas fa-dollar-sign"></i>
        <span>CRÉDITOS</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('credit*') ? 'show' : '' }}" id="creditMenu">
        <a href="{{ route('credit.index') }}" class="menu-item" data-link>Lista de Créditos</a>
        <a href="{{ route('credit.create') }}" class="menu-item" data-link>Nuevo Crédito</a>
        @if(Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('admin'))
        <a href="{{ route('credit.pending_approval') }}" class="menu-item d-flex justify-content-between align-items-center" data-link>
            <span>Pendientes de Aprobación</span>
            @if(isset($pending_credits_count) && $pending_credits_count > 0)
                <span class="badge bg-warning text-dark rounded-pill">{{ $pending_credits_count }}</span>
            @endif
        </a>
        @endif
    </div>

    <!-- PAGOS -->
    <a href="{{ route('payment.index') }}" class="menu-item {{ request()->is('payment*') ? 'active' : '' }}" data-link>
        <i class="fas fa-money-bill"></i>
        <span>PAGOS</span>
    </a>

    <!-- SIMULADOR -->
    <a href="{{ route('simulator.index') }}" class="menu-item {{ request()->is('simulator*') ? 'active' : '' }}" data-link>
        <i class="fas fa-calculator"></i>
        <span>SIMULADOR</span>
    </a>

    <!-- REPORTES -->
    <div class="menu-section menu-item {{ request()->is('reports*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#reportsMenu">
        <i class="fas fa-chart-bar"></i>
        <span>REPORTES</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('reports*') ? 'show' : '' }}" id="reportsMenu">
        <a href="{{ route('reports.index') }}" class="menu-item" data-link>Estadísticas</a>
        <a href="{{ route('reports.cancelled') }}" class="menu-item" data-link>Créditos Cancelados</a>
        <a href="{{ route('reports.disbursements') }}" class="menu-item" data-link>Desembolsos</a>
        <a href="{{ route('reports.active') }}" class="menu-item" data-link>Créditos Activos</a>
        <a href="{{ route('reports.overdue') }}" class="menu-item" data-link>Créditos Vencidos</a>
        <a href="{{ route('reports.to_cancel') }}" class="menu-item" data-link>Por Cancelar</a>
        <a href="{{ route('reports.monthly_close') }}" class="menu-item" data-link>Cierre de Mes</a>
        <a href="{{ route('reports.recovery_and_disbursements') }}" class="menu-item" data-link>Recuperación y Desembolsos</a>
    </div>

    <!-- COBRANZA -->
    <div class="menu-section menu-item {{ request()->is('collection*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#collectionMenu">
        <i class="fas fa-hand-holding-usd"></i>
        <span>COBRANZA</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('collection*') ? 'show' : '' }}" id="collectionMenu">
        <a href="{{ route('collection.actions.index') }}" class="menu-item" data-link>Acciones de Cobro</a>
        <a href="{{ route('collection.actions.create') }}" class="menu-item" data-link>Nueva Acción</a>
    </div>

    <!-- CONFIGURACIÓN -->
    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="menu-section menu-item {{ request()->is('config*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#configMenu">
        <i class="fas fa-cogs"></i>
        <span>CONFIGURACIÓN</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('config*') ? 'show' : '' }}" id="configMenu">
        <a href="{{ route('config.users.index') }}" class="menu-item" data-link>Usuarios</a>
        <a href="{{ route('config.company.edit') }}" class="menu-item" data-link>Empresa</a>
        <a href="{{ route('config.permisos.index') }}" class="menu-item" data-link>Roles y Permisos</a>
    </div>
    @endif

    <!-- SEGURIDAD Y AUDITORÍA -->
    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="menu-section menu-item {{ request()->is('auditoria*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#securityMenu">
        <i class="fas fa-shield-alt"></i>
        <span>SEGURIDAD Y AUDITORÍA</span>
        <i class="fas fa-angle-down ms-auto"></i>
    </div>
    <div class="collapse submenu {{ request()->is('auditoria*') ? 'show' : '' }}" id="securityMenu">
        <a href="{{ route('audit.index') }}" class="menu-item" data-link>Logs de Auditoría</a>
        <a href="{{ route('audit.index') }}" class="menu-item" data-link>Historial de Cambios</a>
    </div>
    @endif
    
    <!-- CERRAR SESIÓN -->
    <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i>
        <span>CERRAR SESIÓN</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <button id="hide-sidebar" class="btn btn-sm"><i class="fas fa-angle-left"></i> Ocultar</button>
</div>
<button id="show-sidebar" class="btn btn-sm hidden"><i class="fas fa-angle-right"></i></button> 