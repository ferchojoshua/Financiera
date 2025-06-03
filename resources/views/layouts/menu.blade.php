<a href="{{ route('home') }}" class="menu-item {{ Request::is('home*') ? 'active' : '' }}">
    <i class="fas fa-home"></i> INICIO
</a>

<div class="menu-section configuracion">
    <i class="fas fa-cog"></i> CONFIGURACIÓN
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('config.index') }}" class="menu-item {{ Request::is('config*') ? 'active' : '' }}">
        <i class="fas fa-building"></i> EMPRESA
    </a>
    <a href="{{ route('config.users.index') }}" class="menu-item {{ Request::is('config/users*') ? 'active' : '' }}">
        <i class="fas fa-users-cog"></i> USUARIOS DEL SISTEMA
    </a>
    <a href="{{ route('config.permisos.index') }}" class="menu-item {{ Request::is('config/permisos*') ? 'active' : '' }}">
        <i class="fas fa-user-shield"></i> PERMISOS DE ACCESO
    </a>
    <a href="{{ route('config.system_preferences') }}" class="menu-item {{ Request::is('config/system-preferences*') ? 'active' : '' }}">
        <i class="fas fa-sliders-h"></i> PREFERENCIAS DEL SISTEMA
    </a>
</div>

<div class="menu-section clientes">
    <i class="fas fa-users"></i> GESTIÓN DE CLIENTES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('client.index') }}" class="menu-item {{ Request::is('client*') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i> CLIENTES
    </a>
    <a href="{{ route('client.create') }}" class="menu-item {{ Request::is('client/create*') ? 'active' : '' }}">
        <i class="fas fa-user-plus"></i> CREAR CLIENTE
    </a>
    <a href="{{ route('credit.index') }}" class="menu-item {{ Request::is('credit*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave"></i> CRÉDITOS
    </a>
    <a href="{{ route('payment.index') }}" class="menu-item {{ Request::is('payment*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-usd"></i> PAGOS
    </a>
    <a href="{{ route('simulator.index') }}" class="menu-item {{ Request::is('simulator*') ? 'active' : '' }}">
        <i class="fas fa-calculator"></i> SIMULADOR
    </a>
</div>

<div class="menu-section pymes">
    <i class="fas fa-building"></i> GESTIÓN DE PYMES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('pymes.clientes') }}" class="menu-item {{ Request::is('pymes/clientes*') ? 'active' : '' }}">
        <i class="fas fa-building"></i> CLIENTES PYMES
    </a>
    <a href="{{ route('pymes.garantias') }}" class="menu-item {{ Request::is('pymes/garantias*') ? 'active' : '' }}">
        <i class="fas fa-shield-alt"></i> GARANTÍAS
    </a>
</div>

<div class="menu-section reportes">
    <i class="fas fa-chart-bar"></i> REPORTES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('reports.index') }}" class="menu-item {{ Request::is('reports*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> ESTADÍSTICAS
    </a>
    <a href="{{ route('reports.cancelled') }}" class="menu-item {{ Request::is('reports/cancelled*') ? 'active' : '' }}">
        <i class="fas fa-ban"></i> CRÉDITOS CANCELADOS
    </a>
    <a href="{{ route('reports.disbursements') }}" class="menu-item {{ Request::is('reports/disbursements*') ? 'active' : '' }}">
        <i class="fas fa-money-check-alt"></i> DESEMBOLSOS
    </a>
    <a href="{{ route('reports.active') }}" class="menu-item {{ Request::is('reports/active*') ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i> CRÉDITOS ACTIVOS
    </a>
    <a href="{{ route('reports.overdue') }}" class="menu-item {{ Request::is('reports/overdue*') ? 'active' : '' }}">
        <i class="fas fa-exclamation-triangle"></i> CRÉDITOS VENCIDOS
    </a>
</div>

<div class="menu-section cobranza">
    <i class="fas fa-tasks"></i> COBRANZA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('collection.actions.index') }}" class="menu-item {{ Request::is('collection/actions*') ? 'active' : '' }}">
        <i class="fas fa-list-alt"></i> ACCIONES DE COBRO
    </a>
    <a href="{{ route('collection.actions.create') }}" class="menu-item {{ Request::is('collection/actions/create*') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i> NUEVA ACCIÓN
    </a>
</div>

<div class="menu-section seguridad">
    <i class="fas fa-shield-alt"></i> SEGURIDAD Y AUDITORÍA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ url('/auditoria') }}" class="menu-item {{ Request::is('auditoria*') ? 'active' : '' }}">
        <i class="fas fa-history"></i> LOGS DE AUDITORÍA
    </a>
</div>

<div class="menu-section rutas">
    <i class="fas fa-route"></i> RUTAS
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('routes.index') }}" class="menu-item {{ Request::is('route*') ? 'active' : '' }}">
        <i class="fas fa-map-marked-alt"></i> LISTADO DE RUTAS
    </a>
    <a href="{{ route('routes.create') }}" class="menu-item {{ Request::is('route/create*') ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i> CREAR RUTA
    </a>
    <a href="{{ route('supervisor.tracker') }}" class="menu-item {{ Request::is('supervisor/tracker*') ? 'active' : '' }}">
        <i class="fas fa-search-location"></i> RASTREO
    </a>
</div>

<div class="menu-section caja">
    <i class="fas fa-cash-register"></i> CAJA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ url('/caja') }}" class="menu-item {{ Request::is('caja*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-alt"></i> MOVIMIENTOS
    </a>
    <a href="{{ route('supervisor.cash') }}" class="menu-item {{ Request::is('supervisor/cash*') ? 'active' : '' }}">
        <i class="fas fa-cash-register"></i> CAJA DIARIA
    </a>
    <a href="{{ route('supervisor.close.index') }}" class="menu-item {{ Request::is('supervisor/close*') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i> CIERRE DIARIO
    </a>
    <a href="{{ route('contabilidad.index') }}" class="menu-item {{ Request::is('contabilidad*') ? 'active' : '' }}">
        <i class="fas fa-book"></i> CONTABILIDAD
    </a>
    <a href="{{ route('wallets.manage') }}" class="menu-item {{ Request::is('wallet/index*') ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> CARTERAS
    </a>
    <a href="{{ route('summary.index') }}" class="menu-item {{ Request::is('summary*') ? 'active' : '' }}">
        <i class="fas fa-file-alt"></i> RESUMEN CARTERAS
    </a>
</div>

<a href="{{ url('/logout') }}" class="menu-item">
    <i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN
</a> 