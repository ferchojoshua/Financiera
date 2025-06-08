<?php
// Función auxiliar para verificar acceso a módulos
$hasAccess = function($module) {
    $user = Auth::user();
    if ($user->isSuperAdmin()) {
        return true;
    }
    if (method_exists($user, 'hasModuleAccess')) {
        return $user->hasModuleAccess($module);
    }
    return in_array($user->role, ['admin', 'superadmin']) || $user->level === 'admin';
};
?>

<a href="{{ route('home') }}" class="menu-item {{ Request::is('home*') ? 'active' : '' }}" data-link>
    <i class="fas fa-home"></i> INICIO
</a>

@if($hasAccess('clientes'))
<div class="menu-section clientes">
    <i class="fas fa-users"></i> GESTIÓN DE CLIENTES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('clients.index') }}" class="menu-item {{ Request::is('clients*') || Request::is('client*') ? 'active' : '' }}" data-link>
        <i class="fas fa-user-friends"></i> CLIENTES
    </a>
    <a href="{{ route('clients.create') }}" class="menu-item {{ Request::is('clients/create*') || Request::is('client/create*') ? 'active' : '' }}" data-link>
        <i class="fas fa-user-plus"></i> CREAR CLIENTE
    </a>
    <a href="{{ route('credit.index') }}" class="menu-item {{ Request::is('credit*') ? 'active' : '' }}" data-link>
        <i class="fas fa-money-bill-wave"></i> CRÉDITOS
    </a>
    <a href="{{ route('payment.index') }}" class="menu-item {{ Request::is('payment*') ? 'active' : '' }}" data-link>
        <i class="fas fa-hand-holding-usd"></i> PAGOS
    </a>
    @if($hasAccess('simulador'))
    <a href="{{ route('simulator.index') }}" class="menu-item {{ Request::is('simulator*') ? 'active' : '' }}" data-link>
        <i class="fas fa-calculator"></i> SIMULADOR
    </a>
    @endif
</div>
@endif

@if($hasAccess('pymes'))
<div class="menu-section pymes">
    <i class="fas fa-building"></i> GESTIÓN DE PYMES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('pymes.clientes') }}" class="menu-item {{ Request::is('pymes/clientes*') ? 'active' : '' }}" data-link>
        <i class="fas fa-building"></i> CLIENTES PYMES
    </a>
    <a href="{{ route('pymes.garantias') }}" class="menu-item {{ Request::is('pymes/garantias*') ? 'active' : '' }}" data-link>
        <i class="fas fa-shield-alt"></i> GARANTÍAS
    </a>
</div>
@endif

@if($hasAccess('cobranzas'))
<div class="menu-section cobranza">
    <i class="fas fa-tasks"></i> COBRANZA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('collection.actions.index') }}" class="menu-item {{ Request::is('collection/actions*') ? 'active' : '' }}" data-link>
        <i class="fas fa-list-alt"></i> ACCIONES DE COBRO
    </a>
    <a href="{{ route('collection.actions.create') }}" class="menu-item {{ Request::is('collection/actions/create*') ? 'active' : '' }}" data-link>
        <i class="fas fa-plus-circle"></i> NUEVA ACCIÓN
    </a>
</div>
@endif

@if($hasAccess('rutas'))
<div class="menu-section rutas">
    <i class="fas fa-route"></i> RUTAS
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('routes.index') }}" class="menu-item {{ Request::is('route*') ? 'active' : '' }}" data-link>
        <i class="fas fa-map-marked-alt"></i> LISTADO DE RUTAS
    </a>
    <a href="{{ route('routes.create') }}" class="menu-item {{ Request::is('route/create*') ? 'active' : '' }}" data-link>
        <i class="fas fa-plus-circle"></i> CREAR RUTA
    </a>
    <a href="{{ route('supervisor.tracker') }}" class="menu-item {{ Request::is('supervisor/tracker*') ? 'active' : '' }}" data-link>
        <i class="fas fa-search-location"></i> RASTREO
    </a>
</div>
@endif

@if($hasAccess('reportes'))
<div class="menu-section reportes">
    <i class="fas fa-chart-bar"></i> REPORTES
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('reports.index') }}" class="menu-item {{ Request::is('reports*') ? 'active' : '' }}" data-link>
        <i class="fas fa-chart-line"></i> ESTADÍSTICAS
    </a>
</div>
@endif

@if($hasAccess('caja'))
<div class="menu-section caja">
    <i class="fas fa-cash-register"></i> CAJA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ url('/caja') }}" class="menu-item {{ Request::is('caja*') ? 'active' : '' }}" data-link>
        <i class="fas fa-money-bill-alt"></i> MOVIMIENTOS
    </a>
    <a href="{{ route('supervisor.cash') }}" class="menu-item {{ Request::is('supervisor/cash*') ? 'active' : '' }}" data-link>
        <i class="fas fa-cash-register"></i> CAJA DIARIA
    </a>
    <a href="{{ route('supervisor.close.index') }}" class="menu-item {{ Request::is('supervisor/close*') ? 'active' : '' }}" data-link>
        <i class="fas fa-calendar-check"></i> CIERRE DIARIO
    </a>
    <a href="{{ route('wallets.manage') }}" class="menu-item {{ Request::is('wallet/index*') ? 'active' : '' }}" data-link>
        <i class="fas fa-wallet"></i> CARTERAS
    </a>
    <a href="{{ route('summary.index') }}" class="menu-item {{ Request::is('summary*') ? 'active' : '' }}" data-link>
        <i class="fas fa-file-alt"></i> RESUMEN CARTERAS
    </a>
</div>
@endif

@if($hasAccess('configuracion'))
<div class="menu-section configuracion">
    <i class="fas fa-cog"></i> CONFIGURACIÓN
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    @if($hasAccess('empresa'))
    <a href="{{ route('config.index') }}" class="menu-item {{ Request::is('config*') ? 'active' : '' }}" data-link>
        <i class="fas fa-building"></i> EMPRESA
    </a>
    @endif
    @if($hasAccess('usuarios'))
    <a href="{{ route('config.users.index') }}" class="menu-item {{ Request::is('config/users*') ? 'active' : '' }}" data-link>
        <i class="fas fa-users-cog"></i> USUARIOS DEL SISTEMA
    </a>
    @endif
    @if($hasAccess('permisos'))
    <a href="{{ route('config.permisos.index') }}" class="menu-item {{ Request::is('config/permisos*') ? 'active' : '' }}" data-link>
        <i class="fas fa-user-shield"></i> PERMISOS DE ACCESO
    </a>
    @endif
</div>
@endif

<a href="{{ url('/logout') }}" class="menu-item">
    <i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN
</a> 