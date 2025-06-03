@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Reporte</h4>
                    <div>
                        <a href="{{ route('pymes.reportes') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#executeReportModal">
                            <i class="fas fa-play"></i> Ejecutar
                        </button>
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Información General</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%">ID:</th>
                                    <td>{{ $reporte->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $reporte->name }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>{{ ucfirst($reporte->report_type) }}</td>
                                </tr>
                                <tr>
                                    <th>Formato de Salida:</th>
                                    <td>{{ strtoupper($reporte->output_format) }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if($reporte->status == 'active')
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Público:</th>
                                    <td>{{ $reporte->is_public ? 'Sí' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Programación:</th>
                                    <td>{{ $reporte->schedule ?: 'No programado' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Metadatos</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%">Creado por:</th>
                                    <td>{{ $reporte->creator ? $reporte->creator->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha de Creación:</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($reporte->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Última Modificación:</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($reporte->updated_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Última Ejecución:</th>
                                    <td>{{ $reporte->last_run_at ? date('d/m/Y H:i', strtotime($reporte->last_run_at)) : 'Nunca' }}</td>
                                </tr>
                                <tr>
                                    <th>Moneda:</th>
                                    <td>
                                        @if(isset($reporte->parameters['currency']))
                                            @if($reporte->parameters['currency'] == 'all')
                                                Todas
                                            @elseif($reporte->parameters['currency'] == 'USD')
                                                Dólares ($)
                                            @elseif($reporte->parameters['currency'] == 'NIO')
                                                Córdobas (C$)
                                            @else
                                                {{ $reporte->parameters['currency'] }}
                                            @endif
                                        @else
                                            No especificada
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Periodo:</th>
                                    <td>
                                        @if(isset($reporte->parameters['time_period']))
                                            @if($reporte->parameters['time_period'] == 'day')
                                                Diario
                                            @elseif($reporte->parameters['time_period'] == 'month')
                                                Mensual
                                            @else
                                                Personalizado
                                            @endif
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Colector:</th>
                                    <td>
                                        @if(isset($reporte->parameters['collector_filter']))
                                            @if($reporte->parameters['collector_filter'] == 'all')
                                                Todos
                                            @else
                                                Específicos ({{ isset($reporte->parameters['specific_collectors']) ? count($reporte->parameters['specific_collectors']) : 0 }})
                                            @endif
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Descripción</h5>
                            <div class="p-3 bg-light rounded">
                                {{ $reporte->description ?: 'Sin descripción' }}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Consulta SQL</h5>
                            <div class="p-3 bg-light rounded">
                                <pre>{{ $reporte->query_string ?: 'No hay consulta SQL definida' }}</pre>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Parámetros</h5>
                            <div class="p-3 bg-light rounded">
                                <pre>{{ json_encode($reporte->parameters, JSON_PRETTY_PRINT) ?: 'No hay parámetros definidos' }}</pre>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Historial de Ejecuciones</h5>
                            @if(count($reporte->executions) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Estado</th>
                                                <th>Tiempo (seg)</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte->executions as $ejecucion)
                                            <tr>
                                                <td>{{ $ejecucion->id }}</td>
                                                <td>{{ date('d/m/Y H:i', strtotime($ejecucion->execution_date)) }}</td>
                                                <td>{{ $ejecucion->executedBy ? $ejecucion->executedBy->name : 'Automático' }}</td>
                                                <td>
                                                    @if($ejecucion->status == 'success')
                                                        <span class="badge bg-success">Éxito</span>
                                                    @elseif($ejecucion->status == 'failed')
                                                        <span class="badge bg-danger">Error</span>
                                                    @else
                                                        <span class="badge bg-warning">En Progreso</span>
                                                    @endif
                                                </td>
                                                <td>{{ $ejecucion->execution_time ?? '-' }}</td>
                                                <td>
                                                    @if($ejecucion->result_file_path)
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                    @if($ejecucion->status == 'failed')
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ $ejecucion->error_message }}">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Este reporte aún no ha sido ejecutado.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ejecutar reporte -->
<div class="modal fade" id="executeReportModal" tabindex="-1" aria-labelledby="executeReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="executeReportModalLabel">Ejecutar Reporte: {{ $reporte->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pymes.reportes.execute', $reporte->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="execute_currency" class="form-label">Moneda</label>
                                <select class="form-control" id="execute_currency" name="execution_params[currency]">
                                    <option value="all">Todas</option>
                                    <option value="USD" {{ isset($reporte->parameters['currency']) && $reporte->parameters['currency'] == 'USD' ? 'selected' : '' }}>Dólares ($)</option>
                                    <option value="NIO" {{ isset($reporte->parameters['currency']) && $reporte->parameters['currency'] == 'NIO' ? 'selected' : '' }}>Córdobas (C$)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="execute_time_period" class="form-label">Periodo de Tiempo</label>
                                <select class="form-control" id="execute_time_period" name="time_period">
                                    <option value="day" {{ isset($reporte->parameters['time_period']) && $reporte->parameters['time_period'] == 'day' ? 'selected' : '' }}>Diario</option>
                                    <option value="month" {{ isset($reporte->parameters['time_period']) && $reporte->parameters['time_period'] == 'month' ? 'selected' : '' }}>Mensual</option>
                                    <option value="custom" {{ isset($reporte->parameters['time_period']) && $reporte->parameters['time_period'] == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sección para periodo diario -->
                    <div class="form-group mb-3 period-section" id="day-section">
                        <label for="execute_date" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="execute_date" name="date" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Sección para periodo mensual -->
                    <div class="form-group mb-3 period-section" id="month-section" style="display: none;">
                        <label for="execute_year_month" class="form-label">Mes</label>
                        <input type="month" class="form-control" id="execute_year_month" name="year_month" value="{{ date('Y-m') }}">
                    </div>

                    <!-- Sección para periodo personalizado -->
                    <div class="row mb-3 period-section" id="custom-section" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="execute_date_from" class="form-label">Fecha Desde</label>
                                <input type="date" class="form-control" id="execute_date_from" name="date_from" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="execute_date_to" class="form-label">Fecha Hasta</label>
                                <input type="date" class="form-control" id="execute_date_to" name="date_to" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="execute_collector_filter" class="form-label">Filtro de Colector</label>
                        <select class="form-control" id="execute_collector_filter" name="execution_params[collector_filter]">
                            <option value="all" {{ isset($reporte->parameters['collector_filter']) && $reporte->parameters['collector_filter'] == 'all' ? 'selected' : '' }}>Todos los colectores</option>
                            <option value="specific" {{ isset($reporte->parameters['collector_filter']) && $reporte->parameters['collector_filter'] == 'specific' ? 'selected' : '' }}>Colectores específicos</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="execute-collector-specific-section" style="display: none;">
                        <label for="execute_specific_collectors" class="form-label">Seleccionar Colectores</label>
                        <select multiple class="form-control" id="execute_specific_collectors" name="execution_params[specific_collectors][]">
                            <!-- Aquí se cargarían dinámicamente los colectores disponibles -->
                            <option value="1">Colector 1</option>
                            <option value="2">Colector 2</option>
                            <option value="3">Colector 3</option>
                        </select>
                        <small class="form-text text-muted">Mantenga presionada la tecla Ctrl para seleccionar múltiples colectores</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play"></i> Ejecutar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestionar secciones de periodo de tiempo
        const timePeriodSelect = document.getElementById('execute_time_period');
        const daySection = document.getElementById('day-section');
        const monthSection = document.getElementById('month-section');
        const customSection = document.getElementById('custom-section');
        
        function updatePeriodSections() {
            // Ocultar todas las secciones
            daySection.style.display = 'none';
            monthSection.style.display = 'none';
            customSection.style.display = 'none';
            
            // Mostrar la sección correspondiente
            if (timePeriodSelect.value === 'day') {
                daySection.style.display = 'block';
            } else if (timePeriodSelect.value === 'month') {
                monthSection.style.display = 'block';
            } else if (timePeriodSelect.value === 'custom') {
                customSection.style.display = 'block';
            }
        }
        
        timePeriodSelect.addEventListener('change', updatePeriodSections);
        
        // Inicializar secciones
        updatePeriodSections();
        
        // Gestionar sección de colectores específicos
        const collectorFilterSelect = document.getElementById('execute_collector_filter');
        const collectorSpecificSection = document.getElementById('execute-collector-specific-section');
        
        collectorFilterSelect.addEventListener('change', function() {
            if (this.value === 'specific') {
                collectorSpecificSection.style.display = 'block';
            } else {
                collectorSpecificSection.style.display = 'none';
            }
        });
        
        // Inicializar estado de colectores
        if (collectorFilterSelect.value === 'specific') {
            collectorSpecificSection.style.display = 'block';
        }
        
        // Abrir el modal automáticamente si se recibe el parámetro execute=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('execute') === '1') {
            const executeModal = new bootstrap.Modal(document.getElementById('executeReportModal'));
            executeModal.show();
        }
    });
</script>
@endpush
@endsection 