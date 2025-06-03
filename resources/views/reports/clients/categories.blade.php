@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reporte de Clientes por Categoría</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Este reporte muestra un análisis de clientes por segmento o categoría.
                    </div>
                    
                    <form method="GET" action="{{ route('clients.report') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category">Categoría</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Todas las categorías</option>
                                        <option value="A">Categoría A</option>
                                        <option value="B">Categoría B</option>
                                        <option value="C">Categoría C</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Fecha Inicio</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Fecha Fin</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Funcionalidad en desarrollo. Pronto estará disponible este reporte.
                    </div>
                    
                    <div class="text-center mt-5 mb-5">
                        <i class="fa fa-chart-pie fa-5x text-muted"></i>
                        <h4 class="mt-3">Vista previa no disponible</h4>
                        <p class="text-muted">La funcionalidad de reportes está siendo implementada.</p>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Volver a Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 