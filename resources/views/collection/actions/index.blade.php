@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gestión de Cobranza</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">TOTAL RECUPERADO</h6>
                                    <h2 class="card-text">${{ number_format($stats['total_overdue'] ?? 0, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">MOROSOS</h6>
                                    <h2 class="card-text">{{ $stats['count'] ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">% RECUPERACIÓN</h6>
                                    <h2 class="card-text">{{ number_format($stats['recovery_percentage'] ?? 0, 0) }}%</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('collection.actions.index') }}" method="GET" class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="route_id">Ruta</label>
                                    <select name="route_id" id="route_id" class="form-control">
                                        <option value="">Todas las rutas</option>
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="overdue_days">Días de atraso</label>
                                    <select name="overdue_days" id="overdue_days" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="1-7" {{ request('overdue_days') == '1-7' ? 'selected' : '' }}>1-7 días</option>
                                        <option value="8-15" {{ request('overdue_days') == '8-15' ? 'selected' : '' }}>8-15 días</option>
                                        <option value="16-30" {{ request('overdue_days') == '16-30' ? 'selected' : '' }}>16-30 días</option>
                                        <option value="31-60" {{ request('overdue_days') == '31-60' ? 'selected' : '' }}>31-60 días</option>
                                        <option value="61+" {{ request('overdue_days') == '61+' ? 'selected' : '' }}>Más de 60 días</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary form-control">
                                        <i class="fa fa-search"></i> Filtrar
                                    </button>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label>&nbsp;</label>
                                    <a href="{{ route('collection.actions.create') }}" class="btn btn-success form-control">
                                        <i class="fa fa-plus"></i> Nueva Acción
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Ruta</th>
                                    <th>Días Atraso</th>
                                    <th>Última Acción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                    <tr>
                                        <td>{{ $credit->user->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($credit->amount_neto, 2) }}</td>
                                        <td>{{ $credit->route->name ?? 'Sin ruta' }}</td>
                                        <td>{{ $credit->days_overdue ?? 'N/A' }}</td>
                                        <td>{{ $credit->last_action_date ?? 'Sin acciones' }}</td>
                                        <td>
                                            @if($credit->status == 'active')
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($credit->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('collection.actions.create', ['credit_id' => $credit->id]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                                <a href="{{ route('credit.show', $credit->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay créditos pendientes de cobranza</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $credits->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 