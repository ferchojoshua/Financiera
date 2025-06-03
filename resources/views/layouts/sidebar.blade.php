@if(Auth::user())
<div class="sidebar-original">
    <div class="sidebar-header">
        <div class="admin-titles">
            <span class="main-title">Super Admin</span>
            <span class="sub-title">Admin</span>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="{{ url('/home') }}" class="menu-link">
            <i class="fa fa-home"></i> INICIO
        </a>
        
        <a href="{{ url('user/create') }}" class="menu-link">
            <i class="fa fa-user-plus"></i> CREAR USUARIO
        </a>
        
        <a href="{{ url('credit/create') }}" class="menu-link">
            <i class="fa fa-credit-card"></i> CREAR CARTERA
        </a>
        
        <a href="{{ url('users') }}" class="menu-link">
            <i class="fa fa-users"></i> USUARIOS
        </a>
        
        <a href="{{ url('wallets') }}" class="menu-link">
            <i class="fa fa-briefcase"></i> CARTERAS
        </a>
        
        <a href="{{ url('routes') }}" class="menu-link">
            <i class="fa fa-map-marker"></i> RUTAS
        </a>
        
        <a href="{{ url('statistics') }}" class="menu-link">
            <i class="fa fa-bar-chart"></i> ESTADÍSTICAS
        </a>
        
        <a href="{{ url('summary') }}" class="menu-link">
            <i class="fa fa-file-text-o"></i> RESUMEN CARTERAS
        </a>
        
        <a href="{{ url('supervisor/summary') }}" class="menu-link">
            <i class="fa fa-eye"></i> RESUMEN SUPERVISOR
        </a>
        
        <a href="{{ url('tracker') }}" class="menu-link">
            <i class="fa fa-map"></i> RASTREO
        </a>
        
        <a href="{{ url('config') }}" class="menu-link">
            <i class="fa fa-cogs"></i> CONFIGURACIÓN
        </a>
        
        <a href="{{ url('pymes') }}" class="menu-link">
            <i class="fa fa-building"></i> PYMES
        </a>
        
        <a href="{{ url('collaterals') }}" class="menu-link">
            <i class="fa fa-shield"></i> GARANTÍAS
        </a>
        
        <a href="{{ url('credits') }}" class="menu-link">
            <i class="fa fa-money"></i> CRÉDITOS
        </a>
        
        <a href="{{ url('payments') }}" class="menu-link">
            <i class="fa fa-dollar"></i> PAGOS
        </a>
        
        <a href="{{ url('collections') }}" class="menu-link">
            <i class="fa fa-calendar-check-o"></i> COBRANZAS
        </a>
        
        <a href="{{ url('accounting') }}" class="menu-link">
            <i class="fa fa-calculator"></i> CONTABILIDAD
        </a>
        
        <a href="{{ url('reports') }}" class="menu-link">
            <i class="fa fa-file-pdf-o"></i> REPORTES
        </a>
        
        <a href="{{ route('logout') }}" class="menu-link" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out"></i> CERRAR SESIÓN
        </a>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<style>
.sidebar-original {
    width: 275px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background-color: #009688;
    color: white;
    overflow-y: auto;
    z-index: 1000;
}

.sidebar-header {
    background-color: #00796B;
    padding: 15px;
    display: flex;
    justify-content: space-between;
}

.admin-titles {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.main-title, .sub-title {
    font-size: 1.2rem;
    font-weight: bold;
}

.sidebar-menu {
    padding: 0;
}

.menu-link {
    display: block;
    padding: 10px 15px;
    color: white;
    text-decoration: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: background-color 0.2s;
    font-size: 0.9rem;
    font-weight: 500;
}

.menu-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    text-decoration: none;
}

.menu-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    color: #89e0d8;
}
</style>
@endif 