@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Reporte de Préstamos Vencidos</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('reports.overdue') }}" class="mb-4">
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
                                    <label for="days_overdue">Días de vencimiento</label>
                                    <select name="days_overdue" id="days_overdue" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="1-7" {{ request('days_overdue') == '1-7' ? 'selected' : '' }}>1 a 7 días</option>
                                        <option value="8-15" {{ request('days_overdue') == '8-15' ? 'selected' : '' }}>8 a 15 días</option>
                                        <option value="16-30" {{ request('days_overdue') == '16-30' ? 'selected' : '' }}>16 a 30 días</option>
                                        <option value="31-60" {{ request('days_overdue') == '31-60' ? 'selected' : '' }}>31 a 60 días</option>
                                        <option value="61-90" {{ request('days_overdue') == '61-90' ? 'selected' : '' }}>61 a 90 días</option>
                                        <option value="90+" {{ request('days_overdue') == '90+' ? 'selected' : '' }}>Más de 90 días</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('reports.overdue') }}" class="btn btn-secondary">
                                    <i class="fa fa-undo"></i> Reiniciar
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Vencido</h5>
                                    <h3 class="card-text">{{ number_format($stats['total_overdue_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Préstamos Vencidos</h5>
                                    <h3 class="card-text">{{ $stats['total_credits'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Promedio Días Vencidos</h5>
                                    <h3 class="card-text">{{ number_format($stats['avg_days_overdue'], 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Mayor Vencimiento</h5>
                                    <h3 class="card-text">{{ $stats['max_days_overdue'] }} días</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de préstamos vencidos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Préstamo</th>
                                    <th>Ruta</th>
                                    <th>Cobrador</th>
                                    <th>Vencimiento</th>
                                    <th>Días Vencidos</th>
                                    <th>Monto Vencido</th>
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
                                    <td>{{ $credit->due_date->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $credit->days_overdue }} días</span>
                                    </td>
                                    <td>{{ number_format($credit->overdue_amount, 2) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('credits.show', $credit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('collection.actions.create', ['credit_id' => $credit->id]) }}" class="btn btn-sm btn-warning">
                                                <i class="fa fa-phone"></i>
                                            </a>
                                            <a href="{{ route('collection.agreements.create', ['credit_id' => $credit->id]) }}" class="btn btn-sm btn-success">
                                                <i class="fa fa-handshake"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No hay préstamos vencidos que coincidan con los filtros aplicados.</td>
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
                        <span>Mostrando {{ $credits->count() }} de {{ $credits->total() }} préstamos vencidos</span>
                        <div>
                            <a href="{{ route('reports.export', ['type' => 'overdue', 'format' => 'excel']) }}" class="btn btn-success">
                                <i class="fa fa-file-excel"></i> Exportar a Excel
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'overdue', 'format' => 'pdf']) }}" class="btn btn-danger">
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