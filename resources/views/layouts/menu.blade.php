<?php
// Función auxiliar para verificar acceso a módulos
$hasAccess = function($module) {
    $user = Auth::user();
    
    // Si es superadmin, tiene acceso a todo
    if ($user->role === 'superadmin' || $user->level === 'admin') {
        return true;
    }

    // Verificar en la tabla role_module_permissions
    $roleId = DB::table('roles')->where('slug', $user->role)->value('id');
    if ($roleId) {
        return DB::table('role_module_permissions')
            ->where('role_id', $roleId)
            ->where('module', $module)
            ->where('has_access', 1)
            ->exists();
    }

    return false;
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
    @if($hasAccess('solicitudes'))
    <a href="{{ route('pymes.solicitudes') }}" class="menu-item {{ Request::is('pymes/solicitudes*') ? 'active' : '' }}" data-link>
        <i class="fas fa-file-alt"></i> SOLICITUDES
    </a>
    @endif
    @if($hasAccess('analisis'))
    <a href="{{ route('pymes.analisis') }}" class="menu-item {{ Request::is('pymes/analisis*') ? 'active' : '' }}" data-link>
        <i class="fas fa-chart-line"></i> ANÁLISIS Y SCORING
    </a>
    @endif
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

@if($hasAccess('productos'))
<div class="menu-section productos">
    <i class="fas fa-box"></i> PRODUCTOS FINANCIEROS
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('pymes.productos') }}" class="menu-item {{ Request::is('pymes/productos*') ? 'active' : '' }}" data-link>
        <i class="fas fa-list"></i> CATÁLOGO DE PRODUCTOS
    </a>
    <a href="{{ route('pymes.productos.create') }}" class="menu-item {{ Request::is('pymes/productos/create*') ? 'active' : '' }}" data-link>
        <i class="fas fa-plus"></i> NUEVO PRODUCTO
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
    @if($hasAccess('acuerdos'))
    <a href="{{ route('collection.agreements.index') }}" class="menu-item {{ Request::is('collection/agreements*') ? 'active' : '' }}" data-link>
        <i class="fas fa-handshake"></i> ACUERDOS DE PAGO
    </a>
    @endif
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
    @if($hasAccess('asignacion_creditos'))
    <a href="{{ route('routes.assign_credits', ['route' => 0]) }}" class="menu-item {{ Request::is('route/*/assign-credits*') ? 'active' : '' }}" data-link>
        <i class="fas fa-tasks"></i> ASIGNACIÓN DE CRÉDITOS
    </a>
    @endif
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
    @if($hasAccess('reportes_cancelados'))
    <a href="{{ route('reports.cancelled') }}" class="menu-item {{ Request::is('reports/cancelled*') ? 'active' : '' }}" data-link>
        <i class="fas fa-ban"></i> CRÉDITOS CANCELADOS
    </a>
    @endif
    @if($hasAccess('reportes_desembolsos'))
    <a href="{{ route('reports.disbursements') }}" class="menu-item {{ Request::is('reports/disbursements*') ? 'active' : '' }}" data-link>
        <i class="fas fa-money-check-alt"></i> DESEMBOLSOS
    </a>
    @endif
    @if($hasAccess('reportes_activos'))
    <a href="{{ route('reports.active') }}" class="menu-item {{ Request::is('reports/active*') ? 'active' : '' }}" data-link>
        <i class="fas fa-check-circle"></i> CRÉDITOS ACTIVOS
    </a>
    @endif
    @if($hasAccess('reportes_vencidos'))
    <a href="{{ route('reports.overdue') }}" class="menu-item {{ Request::is('reports/overdue*') ? 'active' : '' }}" data-link>
        <i class="fas fa-exclamation-triangle"></i> CRÉDITOS VENCIDOS
    </a>
    @endif
</div>
@endif

@if($hasAccess('caja'))
<div class="menu-section caja">
    <i class="fas fa-cash-register"></i> CAJA Y CONTABILIDAD
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
    @if($hasAccess('contabilidad'))
    <a href="{{ route('accounting.index') }}" class="menu-item {{ Request::is('accounting*') ? 'active' : '' }}" data-link>
        <i class="fas fa-book-open"></i> LIBRO MAYOR
    </a>
    @endif
    @if($hasAccess('cierre_mes'))
    <a href="{{ route('accounting.month-close') }}" class="menu-item {{ Request::is('accounting/month-close*') ? 'active' : '' }}" data-link>
        <i class="fas fa-calendar-check"></i> CIERRE DE MES
    </a>
    @endif
    @if($hasAccess('recuperacion_desembolsos'))
    <a href="{{ route('accounting.disbursements') }}" class="menu-item {{ Request::is('accounting/disbursements*') ? 'active' : '' }}" data-link>
        <i class="fas fa-exchange-alt"></i> RECUPERACIÓN Y DESEMBOLSOS
    </a>
    @endif
    <a href="{{ route('wallets.manage') }}" class="menu-item {{ Request::is('wallet/index*') ? 'active' : '' }}" data-link>
        <i class="fas fa-wallet"></i> CARTERAS
    </a>
    <a href="{{ route('summary.index') }}" class="menu-item {{ Request::is('summary*') ? 'active' : '' }}" data-link>
        <i class="fas fa-file-alt"></i> RESUMEN CARTERAS
    </a>
</div>
@endif

@if($hasAccess('seguridad'))
<div class="menu-section seguridad">
    <i class="fas fa-shield-alt"></i> SEGURIDAD Y AUDITORÍA
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ url('/auditoria') }}" class="menu-item {{ Request::is('auditoria*') ? 'active' : '' }}" data-link>
        <i class="fas fa-history"></i> LOGS DE AUDITORÍA
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
    @if($hasAccess('preferencias'))
    <a href="{{ route('config.system_preferences') }}" class="menu-item {{ Request::is('config/system-preferences*') ? 'active' : '' }}" data-link>
        <i class="fas fa-sliders-h"></i> PREFERENCIAS DEL SISTEMA
    </a>
    @endif
</div>
@endif

@if(Auth::user()->role === 'colector' || Auth::user()->level === 'colector')
<div class="menu-section cobrador">
    <i class="fas fa-money-bill"></i> COBRADOR
    <i class="fas fa-chevron-down toggle-icon"></i>
</div>
<div class="submenu">
    <a href="{{ route('collection.actions.index') }}" class="menu-item {{ Request::is('collection/actions*') ? 'active' : '' }}" data-link>
        <i class="fas fa-hand-holding-usd"></i> COBRANZAS
    </a>
    <a href="{{ route('routes.index') }}" class="menu-item {{ Request::is('route*') ? 'active' : '' }}" data-link>
        <i class="fas fa-map-marked-alt"></i> MIS RUTAS
    </a>
    <a href="{{ route('clients.index') }}" class="menu-item {{ Request::is('clients*') || Request::is('client*') ? 'active' : '' }}" data-link>
        <i class="fas fa-users"></i> MIS CLIENTES
    </a>
</div>
@endif

<a href="{{ url('/logout') }}" class="menu-item">
    <i class="fas fa-sign-out-alt"></i> CERRAR SESIÓN
</a>
