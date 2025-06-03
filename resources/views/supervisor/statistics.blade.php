@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Estadísticas</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Visualice estadísticas detalladas del rendimiento de los agentes.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="agent">Agente</label>
                    <select name="agent_id" id="agent" class="form-select">
                        <option value="all">Todos los agentes</option>
                        <!-- Aquí se mostrarán los agentes -->
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="start_date">Fecha Inicio</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date">Fecha Fin</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Cobrado</h5>
                            <h3 class="mb-0">$0.00</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Nuevos Créditos</h5>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">% Recuperación</h5>
                            <h3 class="mb-0">0%</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Clientes Morosos</h5>
                            <h3 class="mb-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Rendimiento por Agente</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                                <p class="text-muted">Seleccione un rango de fechas para ver el gráfico</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Distribución de Créditos</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                                <p class="text-muted">Seleccione un rango de fechas para ver el gráfico</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 