@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reporte de Desempeño de Clientes</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Este reporte muestra el historial crediticio y calificación de los clientes.
                    </div>
                    
                    <form method="GET" action="{{ route('clients.performance') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="rating">Calificación</label>
                                    <select name="rating" id="rating" class="form-control">
                                        <option value="">Todas las calificaciones</option>
                                        <option value="excellent">Excelente</option>
                                        <option value="good">Bueno</option>
                                        <option value="regular">Regular</option>
                                        <option value="poor">Malo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_id">Cliente</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Todos los clientes</option>
                                        <!-- Aquí se cargarían los clientes dinámicamente -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="period">Período</label>
                                    <select name="period" id="period" class="form-control">
                                        <option value="1">Último mes</option>
                                        <option value="3">Últimos 3 meses</option>
                                        <option value="6">Últimos 6 meses</option>
                                        <option value="12">Último año</option>
                                        <option value="all">Todo el historial</option>
                                    </select>
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
                        <i class="fa fa-chart-line fa-5x text-muted"></i>
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