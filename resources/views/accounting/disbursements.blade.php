@extends('layouts.app')

@section('title', 'Desembolsos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Desembolsos</h3>
                </div>
                
                <div class="card-body">
                    <!-- Formulario de filtros -->
                    <form method="GET" action="{{ route('accounting.disbursements') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ request('start_date', date('Y-m-01')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">Fecha Fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ request('end_date', date('Y-m-d')) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('accounting.disbursements') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Resumen de totales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Desembolsado</span>
                                    <span class="info-box-number">$ {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Créditos</span>
                                    <span class="info-box-number">{{ $disbursements->total() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de desembolsos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Ruta</th>
                                    <th class="text-right">Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disbursements as $credit)
                                    <tr>
                                        <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('clients.show', $credit->user_id) }}">
                                                {{ $credit->user->name }}
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
                                        <td class="text-right">$ {{ number_format($credit->amount_neto, 2) }}</td>
                                        <td>
                                            <span class="badge badge-success">Activo</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('credit.show', $credit->id) }}" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay desembolsos para mostrar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $disbursements->appends(request()->except('page'))->links() }}
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('accounting.disbursements.export') }}?{{ http_build_query(request()->all()) }}" 
                               class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Exportar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 