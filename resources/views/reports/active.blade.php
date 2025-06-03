@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reporte de Préstamos Activos</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('reports.active') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="route_id">Ruta</label>
                                    <select name="route_id" id="route_id" class="form-select">
                                        <option value="">Todas las rutas</option>
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="collector_id">Cobrador</label>
                                    <select name="collector_id" id="collector_id" class="form-select">
                                        <option value="">Todos los cobradores</option>
                                        @foreach($collectors as $collector)
                                            <option value="{{ $collector->id }}" {{ request('collector_id') == $collector->id ? 'selected' : '' }}>
                                                {{ $collector->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_filter">Fecha de creación</label>
                                    <select name="date_filter" id="date_filter" class="form-select">
                                        <option value="">Todas las fechas</option>
                                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hoy</option>
                                        <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>Esta semana</option>
                                        <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>Este mes</option>
                                        <option value="last_month" {{ request('date_filter') == 'last_month' ? 'selected' : '' }}>Mes pasado</option>
                                        <option value="this_year" {{ request('date_filter') == 'this_year' ? 'selected' : '' }}>Este año</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('reports.active') }}" class="btn btn-secondary">
                                    <i class="fa fa-undo"></i> Reiniciar
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Préstamos</h5>
                                    <h3 class="card-text">{{ $stats['total_credits'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Capital Activo</h5>
                                    <h3 class="card-text">{{ number_format($stats['total_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Intereses por Cobrar</h5>
                                    <h3 class="card-text">{{ number_format($stats['total_interest'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Promedio por Préstamo</h5>
                                    <h3 class="card-text">{{ number_format($stats['avg_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de préstamos activos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Préstamo</th>
                                    <th>Ruta</th>
                                    <th>Cobrador</th>
                                    <th>Fecha Creación</th>
                                    <th>Monto</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                <tr>
                                    <td>
                                        <a href="{{ route('users.show', $credit->user_id) }}">
                                            {{ $credit->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('credits.show', $credit->id) }}">
                                            {{ $credit->credit_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($credit->route)
                                            <a href="{{ route('routes.show', $credit->route_id) }}">
                                                {{ $credit->route->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Sin ruta</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($credit->route && $credit->route->collector)
                                            {{ $credit->route->collector->name }}
                                        @else
                                            <span class="text-muted">Sin cobrador</span>
                                        @endif
                                    </td>
                                    <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                    <td>{{ number_format($credit->balance, 2) }}</td>
                                    <td>
                                        @if($credit->is_overdue)
                                            <span class="badge bg-danger">Vencido</span>
                                        @else
                                            <span class="badge bg-success">Al día</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('credits.show', $credit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('credits.payments.create', $credit->id) }}" class="btn btn-sm btn-success">
                                                <i class="fa fa-money-bill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay préstamos activos que coincidan con los filtros aplicados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $credits->appends(request()->except('page'))->links() }}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Mostrando {{ $credits->count() }} de {{ $credits->total() }} préstamos activos</span>
                        <div>
                            <a href="{{ route('reports.export', ['type' => 'active', 'format' => 'excel']) }}" class="btn btn-success">
                                <i class="fa fa-file-excel"></i> Exportar a Excel
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'active', 'format' => 'pdf']) }}" class="btn btn-danger">
                                <i class="fa fa-file-pdf"></i> Exportar a PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 