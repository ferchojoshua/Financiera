@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Cierre Mensual</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Este reporte muestra el resumen mensual de operaciones
                    </div>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Filtros</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('reports.monthly_close') }}" method="GET" class="form-inline">
                                        <div class="form-group mb-2 mr-2">
                                            <label for="month" class="mr-2">Mes:</label>
                                            <select name="month" id="month" class="form-control">
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ isset($month) && $month == $i ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group mb-2 mr-2">
                                            <label for="year" class="mr-2">Año:</label>
                                            <select name="year" id="year" class="form-control">
                                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                                    <option value="{{ $i }}" {{ isset($year) && $year == $i ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Resumen de préstamos -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Resumen de Préstamos</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Total de Préstamos:</th>
                                                <td>{{ isset($loansSummary) ? $loansSummary->total_loans : 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>Monto Total:</th>
                                                <td>{{ isset($loansSummary) ? number_format($loansSummary->total_amount, 2) : '0.00' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tasa de Interés Promedio:</th>
                                                <td>{{ isset($loansSummary) ? number_format($loansSummary->avg_interest_rate, 2) : '0.00' }}%</td>
                                            </tr>
                                            <tr>
                                                <th>Préstamos Activos:</th>
                                                <td>{{ isset($loansSummary) ? $loansSummary->active_loans : 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>Préstamos Cancelados:</th>
                                                <td>{{ isset($loansSummary) ? $loansSummary->cancelled_loans : 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumen de pagos -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Resumen de Pagos</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Total de Pagos:</th>
                                                <td>{{ isset($paymentsSummary) ? $paymentsSummary->total_payments : 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>Monto Total Recaudado:</th>
                                                <td>{{ isset($paymentsSummary) ? number_format($paymentsSummary->total_amount, 2) : '0.00' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pagos de Principal:</th>
                                                <td>{{ isset($paymentsSummary) ? number_format($paymentsSummary->principal_amount, 2) : '0.00' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pagos de Interés:</th>
                                                <td>{{ isset($paymentsSummary) ? number_format($paymentsSummary->interest_amount, 2) : '0.00' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Moras:</th>
                                                <td>{{ isset($paymentsSummary) ? number_format($paymentsSummary->late_fee_amount, 2) : '0.00' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gráficos (opcional) -->
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
                        <a href="{{ route('reports.export', ['monthly_close', 'pdf']) }}?month={{ $month ?? date('m') }}&year={{ $year ?? date('Y') }}" class="btn btn-danger">
                            <i class="fa fa-file-pdf"></i> Exportar a PDF
                        </a>
                        <a href="{{ route('reports.export', ['monthly_close', 'excel']) }}?month={{ $month ?? date('m') }}&year={{ $year ?? date('Y') }}" class="btn btn-success ml-2">
                            <i class="fa fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 