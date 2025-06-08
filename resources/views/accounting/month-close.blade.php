@extends('layouts.app')

@section('title', 'Cierre Mensual')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cierre Mensual - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h3>
                </div>
                
                <div class="card-body">
                    <!-- Formulario de filtros -->
                    <form method="GET" action="{{ route('accounting.month-close') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="month">Mes</label>
                                    <select name="month" id="month" class="form-control">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" 
                                                    {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::createFromDate(null, $i, 1)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year">Año</label>
                                    <select name="year" id="year" class="form-control">
                                        @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Resumen de totales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ingresos</span>
                                    <span class="info-box-number">$ {{ number_format($totals['ingresos'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-minus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Gastos</span>
                                    <span class="info-box-number">$ {{ number_format($totals['gastos'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-sync"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ajustes</span>
                                    <span class="info-box-number">$ {{ number_format($totals['ajustes'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen por categorías -->
                    <div class="row">
                        <div class="col-12">
                            <h4>Resumen por Categorías</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($entriesByCategory as $entry)
                                            <tr>
                                                <td>{{ $entry->category }}</td>
                                                <td>
                                                    @switch($entry->entry_type)
                                                        @case('ingreso')
                                                            <span class="badge badge-success">Ingreso</span>
                                                            @break
                                                        @case('gasto')
                                                            <span class="badge badge-danger">Gasto</span>
                                                            @break
                                                        @case('ajuste')
                                                            <span class="badge badge-warning">Ajuste</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td class="text-right">$ {{ number_format($entry->total, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No hay registros para mostrar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                            <button type="button" class="btn btn-success" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 