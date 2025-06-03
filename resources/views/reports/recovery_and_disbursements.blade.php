@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Análisis de Recuperación y Desembolsos</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Este reporte muestra un análisis comparativo de las recuperaciones vs. los desembolsos en un período determinado
                    </div>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Filtros</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('reports.recovery_and_disbursements') }}" method="GET" class="form-inline">
                                        <div class="form-group mb-2 mr-2">
                                            <label for="start_date" class="mr-2">Fecha Inicio:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                                value="{{ $startDate ?? date('Y-m-01') }}">
                                        </div>
                                        <div class="form-group mb-2 mr-2">
                                            <label for="end_date" class="mr-2">Fecha Fin:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                                value="{{ $endDate ?? date('Y-m-d') }}">
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Desembolsos en el Período</h5>
                                    <h2 class="display-4">
                                        @if(isset($disbursements))
                                            {{ number_format($disbursements->sum('total_amount'), 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </h2>
                                    <p>Total de operaciones: 
                                        @if(isset($disbursements))
                                            {{ $disbursements->sum('count') }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4 bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Recuperaciones en el Período</h5>
                                    <h2 class="display-4">
                                        @if(isset($recoveries))
                                            {{ number_format($recoveries->sum('total_amount'), 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </h2>
                                    <p>Total de operaciones: 
                                        @if(isset($recoveries))
                                            {{ $recoveries->sum('count') }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de desembolsos -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Desembolsos por Día</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Cantidad</th>
                                                    <th>Monto Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($disbursements) && count($disbursements) > 0)
                                                    @foreach($disbursements as $item)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                                            <td>{{ $item->count }}</td>
                                                            <td>{{ number_format($item->total_amount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center">No hay datos disponibles</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabla de recuperaciones -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Recuperaciones por Día</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Cantidad</th>
                                                    <th>Monto Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($recoveries) && count($recoveries) > 0)
                                                    @foreach($recoveries as $item)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                                            <td>{{ $item->count }}</td>
                                                            <td>{{ number_format($item->total_amount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center">No hay datos disponibles</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráfico (opcional) -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">Análisis Visual</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-light border">
                                        <p>Los gráficos estarán disponibles en una próxima actualización.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botones de exportación -->
                    <div class="mt-4">
                        <a href="{{ route('reports.export', ['recovery_and_disbursements', 'pdf']) }}?start_date={{ $startDate ?? date('Y-m-01') }}&end_date={{ $endDate ?? date('Y-m-d') }}" class="btn btn-danger">
                            <i class="fa fa-file-pdf"></i> Exportar a PDF
                        </a>
                        <a href="{{ route('reports.export', ['recovery_and_disbursements', 'excel']) }}?start_date={{ $startDate ?? date('Y-m-01') }}&end_date={{ $endDate ?? date('Y-m-d') }}" class="btn btn-success ml-2">
                            <i class="fa fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 