@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Reportes del Sistema</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Seleccione el tipo de reporte que desea generar
                    </div>
                    
                    <div class="row">
                        <!-- Reportes de Préstamos -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Reportes de Préstamos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('reports.cancelled') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-check-circle text-success mr-2"></i>
                                                Préstamos Cancelados
                                                <p class="text-muted small mb-0">Préstamos que han sido pagados completamente</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('reports.disbursements') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-money-bill-wave text-primary mr-2"></i>
                                                Desembolsos
                                                <p class="text-muted small mb-0">Préstamos desembolsados en un período</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('reports.active') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-file-invoice-dollar text-info mr-2"></i>
                                                Préstamos Activos
                                                <p class="text-muted small mb-0">Préstamos que están actualmente en curso</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('reports.overdue') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-exclamation-triangle text-danger mr-2"></i>
                                                Préstamos Vencidos
                                                <p class="text-muted small mb-0">Préstamos con pagos atrasados</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('reports.to_cancel') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-hourglass-end text-warning mr-2"></i>
                                                Por Cancelar
                                                <p class="text-muted small mb-0">Préstamos próximos a vencer</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reportes Financieros -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Reportes Financieros</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('reports.monthly_close') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-calendar-check text-success mr-2"></i>
                                                Cierre de Mes
                                                <p class="text-muted small mb-0">Resumen mensual de operaciones</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('reports.recovery_and_disbursements') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-chart-line text-info mr-2"></i>
                                                Recuperación y Desembolsos
                                                <p class="text-muted small mb-0">Análisis comparativo de recuperaciones vs. desembolsos</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reportes de Clientes -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Reportes de Clientes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="{{ route('clients.report') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-users text-primary mr-2"></i>
                                                Clientes por Categoría
                                                <p class="text-muted small mb-0">Análisis de clientes por segmento</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('clients.performance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fa fa-medal text-warning mr-2"></i>
                                                Desempeño de Clientes
                                                <p class="text-muted small mb-0">Historial crediticio y calificación de clientes</p>
                                            </div>
                                            <span class="badge badge-primary badge-pill">
                                                <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Herramientas de Exportación -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">Herramientas de Exportación</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-light border">
                                                <h6><i class="fa fa-file-excel text-success mr-2"></i> Exportar a Excel</h6>
                                                <p class="small mb-0">Exporta cualquier reporte a formato Excel para análisis más detallados.</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-light border">
                                                <h6><i class="fa fa-file-pdf text-danger mr-2"></i> Exportar a PDF</h6>
                                                <p class="small mb-0">Genera documentos PDF de los reportes para impresión o compartir.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .list-group-item {
        transition: all 0.3s;
    }
    .list-group-item:hover {
        transform: translateX(5px);
        background-color: #f8f9fa;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        font-weight: 600;
    }
</style>
@endsection 