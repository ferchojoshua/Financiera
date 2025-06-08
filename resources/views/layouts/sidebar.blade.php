<div class="sidebar" data-color="green" data-background-color="white">
    <div class="logo">
        <a href="{{ route('home') }}" class="simple-text logo-normal">
            Sistema de Préstamos
        </a>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <!-- INICIO -->
            <li class="nav-item {{ request()->is('/') || request()->is('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-home"></i>
                    <p>INICIO</p>
                </a>
            </li>

            <!-- CLIENTES -->
            <li class="nav-item {{ request()->is('client*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#clientMenu">
                    <i class="fas fa-users"></i>
                    <p>CLIENTES <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('client*') ? 'show' : '' }}" id="clientMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.index') }}">
                                <i class="fas fa-list"></i>
                                <p>Lista de Clientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.create') }}">
                                <i class="fas fa-user-plus"></i>
                                <p>Nuevo Cliente</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.types') }}">
                                <i class="fas fa-tags"></i>
                                <p>Tipos de Cliente</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- RUTAS -->
            <li class="nav-item {{ request()->is('routes*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#routesMenu">
                    <i class="fas fa-map-marked-alt"></i>
                    <p>RUTAS <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('routes*') ? 'show' : '' }}" id="routesMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('routes.index') }}">
                                <i class="fas fa-list"></i>
                                <p>Lista de Rutas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('routes.create') }}">
                                <i class="fas fa-plus"></i>
                                <p>Nueva Ruta</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('routes.map') }}">
                                <i class="fas fa-map"></i>
                                <p>Mapa de Rutas</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- SUCURSALES -->
            <li class="nav-item {{ request()->is('branches*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#branchesMenu">
                    <i class="fas fa-building"></i>
                    <p>SUCURSALES <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('branches*') ? 'show' : '' }}" id="branchesMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('branches.index') }}">
                                <i class="fas fa-list"></i>
                                <p>Lista de Sucursales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('branches.create') }}">
                                <i class="fas fa-plus"></i>
                                <p>Nueva Sucursal</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- CRÉDITOS -->
            <li class="nav-item {{ request()->is('credit*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#creditMenu">
                    <i class="fas fa-dollar-sign"></i>
                    <p>CRÉDITOS <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('credit*') ? 'show' : '' }}" id="creditMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('credit.index') }}">
                                <i class="fas fa-list"></i>
                                <p>Lista de Créditos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('credit.create') }}">
                                <i class="fas fa-plus"></i>
                                <p>Nuevo Crédito</p>
                            </a>
                        </li>
                        @if(Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('admin'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('credit.pending_approval') }}">
                                <i class="fas fa-clock"></i>
                                <p>Pendientes de Aprobación</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>

            <!-- PAGOS -->
            <li class="nav-item {{ request()->is('payment*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('payment.index') }}">
                    <i class="fas fa-money-bill"></i>
                    <p>PAGOS</p>
                </a>
            </li>

            <!-- SIMULADOR -->
            <li class="nav-item {{ request()->is('simulator*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('simulator.index') }}">
                    <i class="fas fa-calculator"></i>
                    <p>SIMULADOR</p>
                </a>
            </li>

            <!-- REPORTES -->
            <li class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#reportsMenu">
                    <i class="fas fa-chart-bar"></i>
                    <p>REPORTES <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('reports*') ? 'show' : '' }}" id="reportsMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.statistics') }}">
                                <i class="fas fa-chart-line"></i>
                                <p>Estadísticas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.loans.cancelled') }}">
                                <i class="fas fa-check-circle"></i>
                                <p>Créditos Cancelados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.loans.disbursements') }}">
                                <i class="fas fa-money-bill-wave"></i>
                                <p>Desembolsos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.loans.active') }}">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <p>Créditos Activos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.loans.overdue') }}">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Créditos Vencidos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.loans.tocancel') }}">
                                <i class="fas fa-clock"></i>
                                <p>Por Cancelar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.financial.monthly') }}">
                                <i class="fas fa-calendar-check"></i>
                                <p>Cierre de Mes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.financial.recovery') }}">
                                <i class="fas fa-sync"></i>
                                <p>Recuperación y Desembolsos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reports.financial.balance') }}">
                                <i class="fas fa-balance-scale"></i>
                                <p>Balance General</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- COBRANZA -->
            <li class="nav-item {{ request()->is('collection*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#collectionMenu">
                    <i class="fas fa-hand-holding-usd"></i>
                    <p>COBRANZA <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('collection*') ? 'show' : '' }}" id="collectionMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('collection.actions.index') }}">
                                <i class="fas fa-list-alt"></i>
                                <p>Acciones de Cobro</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('collection.actions.create') }}">
                                <i class="fas fa-plus"></i>
                                <p>Nueva Acción</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('collection.effectiveness') }}">
                                <i class="fas fa-chart-pie"></i>
                                <p>Efectividad de Cobro</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('collection.delinquency') }}">
                                <i class="fas fa-map"></i>
                                <p>Morosidad por Zona</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- CONFIGURACIÓN -->
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <li class="nav-item {{ request()->is('config*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#configMenu">
                    <i class="fas fa-cogs"></i>
                    <p>CONFIGURACIÓN <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('config*') ? 'show' : '' }}" id="configMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('config.users.index') }}">
                                <i class="fas fa-users"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('config.company.edit') }}">
                                <i class="fas fa-building"></i>
                                <p>Empresa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('config.roles.index') }}">
                                <i class="fas fa-user-tag"></i>
                                <p>Roles y Permisos</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            <!-- SEGURIDAD Y AUDITORÍA -->
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <li class="nav-item {{ request()->is('security*') ? 'active' : '' }}">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#securityMenu">
                    <i class="fas fa-shield-alt"></i>
                    <p>SEGURIDAD Y AUDITORÍA <i class="fas fa-angle-down"></i></p>
                </a>
                <div class="collapse {{ request()->is('security*') ? 'show' : '' }}" id="securityMenu">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('security.audit.index') }}">
                                <i class="fas fa-clipboard-list"></i>
                                <p>Registro de Actividades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('security.audit.history') }}">
                                <i class="fas fa-history"></i>
                                <p>Historial de Acciones</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            @endif

            <!-- CERRAR SESIÓN -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <p>CERRAR SESIÓN</p>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>

<!-- Estilos específicos para el menú -->
<style>
:root {
    --menu-bg: #10775c;
    --menu-hover: #0a5640;
    --menu-text: #ffffff;
}

.sidebar {
    background-color: var(--menu-bg) !important;
}

.sidebar .nav-link {
    color: var(--menu-text) !important;
    padding: 10px 15px;
    margin: 5px 15px;
    border-radius: 3px;
    font-size: 14px;
}

.sidebar .nav-link i {
    color: var(--menu-text) !important;
    margin-right: 10px;
}

.sidebar .nav-link p {
    margin: 0;
    font-weight: 500;
    color: var(--menu-text) !important;
}

.sidebar .nav-link:hover {
    background-color: var(--menu-hover) !important;
}

.sidebar .nav-item.active > .nav-link {
    background-color: var(--menu-hover) !important;
    box-shadow: 0 4px 20px 0px rgba(0, 0, 0, 0.14), 0 7px 10px -5px rgba(16, 119, 92, 0.4);
}

/* Estilos para submenús */
.sidebar .collapse {
    display: block !important;
    visibility: visible !important;
    height: auto !important;
    opacity: 1 !important;
}

.sidebar .collapse .nav-link {
    padding-left: 30px;
}

.sidebar .fa-angle-down {
    float: right;
    margin-top: 3px;
}

.sidebar .collapse.show {
    background-color: var(--menu-hover);
}

/* Ajustes para submenús */
.sidebar .collapse .nav {
    margin-top: 0;
}

.sidebar .collapse .nav-item {
    margin: 0;
}

.sidebar .collapse .nav-link {
    padding-left: 45px;
    font-size: 13px;
}

/* Asegurar visibilidad de submenús */
.sidebar .collapse .nav {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    margin-left: 15px;
    padding-left: 0;
}

.sidebar .collapse .nav-link {
    padding-left: 30px;
}
</style> 