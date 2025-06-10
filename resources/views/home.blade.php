@extends('layouts.app')

@section('admin-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección admin-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('agent-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección agent-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('supervisor-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección supervisor-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('agent-resume')
    @if(isset($base_agent))
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">DISPONIBLE (CAJA)</h5>
                        <h3 class="mb-0">
                            ${{ number_format($base_agent - $total_bill, 2) }}
                            @if($total_summary > 0)
                                <small class="text-white">
                                    + ${{ number_format($total_summary, 2) }} = ${{ number_format(($base_agent - $total_bill) + $total_summary, 2) }}
                                </small>
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL COBRADO</h5>
                        <h3 class="mb-0">${{ number_format($total_summary, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL GASTOS</h5>
                        <h3 class="mb-0">${{ number_format($total_bill, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-info">
            <h2 class="page-title">Panel de Control</h2>
            <div class="user-meta">
                <p class="welcome-text">Bienvenido, {{ Auth::user()->name }}</p>
                <p class="date-text">{{ now()->format('d M, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-card money-card">
            <div class="stat-icon-container">
                <i class="fas fa-dollar-sign stat-icon"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">${{ number_format($totalRecuperado, 2) }}</div>
                <div class="stat-label">Total Recuperado</div>
            </div>
        </div>

        <div class="stat-card users-card">
            <div class="stat-icon-container">
                <i class="fas fa-users stat-icon"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $totalMorosos }}</div>
                <div class="stat-label">Morosos</div>
            </div>
        </div>

        <div class="stat-card chart-card">
            <div class="stat-icon-container">
                <i class="fas fa-chart-line stat-icon"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $porcentajeRecuperacion }}%</div>
                <div class="stat-label">% Recuperación</div>
            </div>
        </div>

        <div class="stat-card clients-card">
            <div class="stat-icon-container">
                <i class="fas fa-user-check stat-icon"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $clientesActivos }}</div>
                <div class="stat-label">Clientes Activos</div>
            </div>
        </div>
    </div>

    <!-- Comparativas -->
    <div class="comparison-grid">
        <div class="comparison-card">
            <div class="comparison-header">
                Comparativa Recuperado vs Desembolsado
            </div>
            <div class="comparison-body">
                <div class="comparison-item">
                    <div class="comparison-value">${{ number_format($totalDesembolsado, 2) }}</div>
                    <div class="comparison-label">Total Desembolsado</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-value">${{ number_format($totalRecuperado, 2) }}</div>
                    <div class="comparison-label">Total Recuperado</div>
                </div>
            </div>
            <div class="progress-container">
                @php
                    $porcentaje = $totalDesembolsado > 0 ? ($totalRecuperado / $totalDesembolsado) * 100 : 0;
                    $porcentaje = min($porcentaje, 100);
                @endphp
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{ $porcentaje }}%" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">{{ round($porcentaje) }}%</div>
                </div>
            </div>
        </div>

        <div class="comparison-card">
            <div class="comparison-header">
                Resumen de Cobranza
            </div>
            <div class="comparison-body">
                <div class="comparison-item">
                    <div class="comparison-value">{{ $clientesActivos }}</div>
                    <div class="comparison-label">Préstamos Activos</div>
                </div>
                <div class="comparison-item">
                    <div class="comparison-value">{{ $totalMorosos }}</div>
                    <div class="comparison-label">Préstamos en Mora</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="activity-grid">
        <div class="activity-card">
            <div class="activity-header">
                <i class="fas fa-money-bill-wave"></i> Últimos Pagos
            </div>
            <div class="activity-body">
                @if(count($ultimosPagos) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosPagos as $pago)
                            <tr>
                                <td>{{ $pago->cliente_nombre }} {{ $pago->cliente_apellido }}</td>
                                <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-inbox empty-icon"></i>
                    <p>No hay pagos registrados</p>
                </div>
                @endif
            </div>
        </div>

        <div class="activity-card">
            <div class="activity-header">
                <i class="fas fa-exclamation-triangle"></i> Morosos Recientes
            </div>
            <div class="activity-body">
                @if(count($morososRecientes) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Días</th>
                                <th>Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($morososRecientes as $moroso)
                            <tr>
                                <td>{{ $moroso->cliente }}</td>
                                <td>{{ $moroso->dias_atraso }}</td>
                                <td class="text-right">${{ number_format($moroso->monto_pendiente, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-check-circle empty-icon"></i>
                    <p>No hay clientes morosos</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Variables para manejar temas claro/oscuro */
:root {
    --text-primary: #333;
    --text-secondary: #666;
    --bg-primary: #fff;
    --bg-secondary: #f8f9fa;
    --border-color: #e0e0e0;
    --card-shadow: 0 2px 8px rgba(0,0,0,0.1);
    --header-bg: #0e8772;
    --header-text: #fff;
    
    /* Colores para tarjetas */
    --money-bg: #0e8772;
    --users-bg: #2980b9;
    --chart-bg: #8e44ad;
    --clients-bg: #27ae60;
    
    /* Colores para tablas */
    --table-header-bg: rgba(14, 135, 114, 0.1);
    --table-hover-bg: rgba(14, 135, 114, 0.05);
    --table-border: #eee;
}

/* Tema oscuro */
@media (prefers-color-scheme: dark) {
    :root {
        --text-primary: #f0f0f0;
        --text-secondary: #aaa;
        --bg-primary: #1e1e1e;
        --bg-secondary: #2d2d2d;
        --border-color: #444;
        --card-shadow: 0 2px 8px rgba(0,0,0,0.3);
        --header-bg: #0e8772;
        --header-text: #fff;
        
        /* Colores para tablas */
        --table-header-bg: rgba(14, 135, 114, 0.2);
        --table-hover-bg: rgba(14, 135, 114, 0.1);
        --table-border: #444;
    }
}

/* Estilos generales del dashboard */
.dashboard-container {
    padding: 1.5rem;
    color: var(--text-primary);
}

.dashboard-header {
    margin-bottom: 1.5rem;
}

.page-title {
    color: var(--text-primary);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.welcome-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.date-text {
    color: var(--text-secondary);
    font-size: 0.813rem;
    margin-bottom: 0;
}

/* Tarjetas de estadísticas */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    background-color: var(--bg-primary);
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    padding: 1.25rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    margin-right: 1rem;
    color: white;
}

.money-card .stat-icon-container {
    background-color: var(--money-bg);
}

.users-card .stat-icon-container {
    background-color: var(--users-bg);
}

.chart-card .stat-icon-container {
    background-color: var(--chart-bg);
}

.clients-card .stat-icon-container {
    background-color: var(--clients-bg);
}

.stat-icon {
    font-size: 1.5rem;
}

.stat-info {
    flex-grow: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

/* Tarjetas de comparativas */
.comparison-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.comparison-card {
    background-color: var(--bg-primary);
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
}

.comparison-header {
    background-color: var(--header-bg);
    color: var(--header-text);
    padding: 1rem;
    font-weight: 500;
}

.comparison-body {
    padding: 1rem;
    display: flex;
    flex-wrap: wrap;
}

.comparison-item {
    flex: 1;
    min-width: 120px;
    padding: 0.5rem;
}

.comparison-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.comparison-label {
    font-size: 0.813rem;
    color: var(--text-secondary);
}

.progress-container {
    padding: 0 1rem 1rem;
}

.progress {
    height: 8px;
    border-radius: 4px;
    background-color: var(--bg-secondary);
}

/* Tarjetas de actividad */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.activity-card {
    background-color: var(--bg-primary);
    border-radius: 8px;
    box-shadow: var(--card-shadow);
    overflow: hidden;
}

.activity-header {
    background-color: var(--header-bg);
    color: var(--header-text);
    padding: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
}

.activity-header i {
    margin-right: 0.5rem;
}

.activity-body {
    padding: 1rem;
}

/* Tablas */
.table {
    color: var(--text-primary);
    margin-bottom: 0;
}

.table thead th {
    background-color: var(--table-header-bg);
    border-bottom: 1px solid var(--table-border);
    padding: 0.75rem;
    font-weight: 500;
}

.table tbody td {
    padding: 0.75rem;
    border-top: 1px solid var(--table-border);
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: var(--table-hover-bg);
}

.text-right {
    text-align: right;
}

/* Estado vacío */
.empty-state {
    padding: 2rem;
    text-align: center;
    color: var(--text-secondary);
}

.empty-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

/* Adaptación a móviles */
@media (max-width: 768px) {
    .stats-grid, .comparison-grid, .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-body {
        flex-direction: column;
    }
    
    .comparison-item {
        width: 100%;
        margin-bottom: 1rem;
    }
}
</style>
@endsection
