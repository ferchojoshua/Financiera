@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Reporte de Desembolsos</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" action="{{ route('reports.disbursements') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from">Fecha Desde</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from', date('Y-m-01')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to">Fecha Hasta</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to', date('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="branch_id">Sucursal</label>
                                    <select name="branch_id" id="branch_id" class="form-select">
                                        <option value="">Todas las sucursales</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('reports.disbursements') }}" class="btn btn-secondary">
                                    <i class="fa fa-undo"></i> Reiniciar
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Desembolsado</h5>
                                    <h3 class="card-text">{{ number_format($stats['total_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Número de Préstamos</h5>
                                    <h3 class="card-text">{{ $stats['count'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Promedio por Préstamo</h5>
                                    <h3 class="card-text">{{ number_format($stats['avg_amount'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Préstamos Nuevos</h5>
                                    <h3 class="card-text">{{ $stats['new_clients'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de desembolsos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Préstamo</th>
                                    <th>Sucursal</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
                                    <th>Aprobado por</th>
                                    <th>Método de pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                <tr>
                                    <td>{{ $credit->disbursement_date ? date('d/m/Y', strtotime($credit->disbursement_date)) : 'N/A' }}</td>
                                    <td>
                                        @if(isset($credit->user))
                                            {{ $credit->user->name }} {{ $credit->user->last_name }}
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('credit.show', $credit->id) }}">
                                            {{ $credit->id }}
                                        </a>
                                    </td>
                                    <td>
                                        @if(isset($credit->branch))
                                            {{ $credit->branch->name }}
                                        @else
                                            <span class="text-muted">Sin sucursal</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                    <td>
                                        @if($credit->is_renewal)
                                            <span class="badge bg-warning">Renovación</span>
                                        @else
                                            <span class="badge bg-success">Nuevo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($credit->approved_by)
                                            {{ $credit->approver->name ?? 'N/A' }}
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($credit->payment_method ?? 'cash')
                                            @case('cash')
                                                Efectivo
                                                @break
                                            @case('bank_transfer')
                                                Transferencia Bancaria
                                                @break
                                            @case('check')
                                                Cheque
                                                @break
                                            @default
                                                {{ $credit->payment_method ?? 'N/A' }}
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('credit.show', $credit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No hay desembolsos que coincidan con los filtros aplicados.</td>
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
                        <span>Mostrando {{ $credits->count() }} de {{ $credits->total() }} desembolsos</span>
                        <div>
                            <a href="{{ route('reports.export', ['type' => 'disbursements', 'format' => 'excel']) }}" class="btn btn-success">
                                <i class="fa fa-file-excel"></i> Exportar a Excel
                            </a>
                            <a href="{{ route('reports.export', ['type' => 'disbursements', 'format' => 'pdf']) }}" class="btn btn-danger">
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