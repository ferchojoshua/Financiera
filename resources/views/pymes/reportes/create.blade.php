@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Crear Nuevo Reporte</h4>
                    <div>
                        <a href="{{ route('pymes.reportes') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pymes.reportes.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nombre del Reporte *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="report_type" class="form-label">Tipo de Reporte *</label>
                                    <select class="form-control" id="report_type" name="report_type" required>
                                        <option value="portfolio" {{ old('report_type') == 'portfolio' ? 'selected' : '' }}>Cartera</option>
                                        <option value="collector" {{ old('report_type') == 'collector' ? 'selected' : '' }}>Colectores</option>
                                        <option value="financial" {{ old('report_type') == 'financial' ? 'selected' : '' }}>Financiero</option>
                                        <option value="operational" {{ old('report_type') == 'operational' ? 'selected' : '' }}>Operacional</option>
                                        <option value="regulatory" {{ old('report_type') == 'regulatory' ? 'selected' : '' }}>Regulatorio</option>
                                        <option value="custom" {{ old('report_type') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="output_format" class="form-label">Formato de Salida *</label>
                                    <select class="form-control" id="output_format" name="output_format" required>
                                        <option value="pdf" {{ old('output_format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                        <option value="excel" {{ old('output_format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                        <option value="csv" {{ old('output_format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                        <option value="html" {{ old('output_format') == 'html' ? 'selected' : '' }}>HTML</option>
                                        <option value="json" {{ old('output_format') == 'json' ? 'selected' : '' }}>JSON</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="currency" class="form-label">Moneda *</label>
                                    <select class="form-control" id="currency" name="currency" required>
                                        <option value="all" {{ old('currency') == 'all' ? 'selected' : '' }}>Todas</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>Dólares ($)</option>
                                        <option value="NIO" {{ old('currency') == 'NIO' ? 'selected' : '' }}>Córdobas (C$)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="time_period" class="form-label">Periodo de Tiempo *</label>
                                    <select class="form-control" id="time_period" name="time_period" required>
                                        <option value="day" {{ old('time_period') == 'day' ? 'selected' : '' }}>Diario</option>
                                        <option value="month" {{ old('time_period') == 'month' ? 'selected' : '' }}>Mensual</option>
                                        <option value="custom" {{ old('time_period') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="collector_filter" class="form-label">Filtro de Colector</label>
                                    <select class="form-control" id="collector_filter" name="collector_filter">
                                        <option value="all" {{ old('collector_filter') == 'all' ? 'selected' : '' }}>Todos los colectores</option>
                                        <option value="specific" {{ old('collector_filter') == 'specific' ? 'selected' : '' }}>Colectores específicos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Estado *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3 collector-specific-section" style="display: none;">
                            <label for="specific_collectors" class="form-label">Seleccionar Colectores</label>
                            <select multiple class="form-control" id="specific_collectors" name="specific_collectors[]">
                                <!-- Esta sección se llenará con AJAX o al cargar la página -->
                                <!-- Ejemplo: -->
                                <option value="1">Colector 1</option>
                                <option value="2">Colector 2</option>
                                <option value="3">Colector 3</option>
                            </select>
                            <small class="form-text text-muted">Mantenga presionada la tecla Ctrl para seleccionar múltiples colectores</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="query_string" class="form-label">Consulta SQL</label>
                            <textarea class="form-control" id="query_string" name="query_string" rows="5">{{ old('query_string') }}</textarea>
                            <small class="form-text text-muted">Introduzca la consulta SQL para el reporte. Puede usar parámetros con sintaxis {parameter_name}. Utilice {collector_id} para filtrar por colector, {currency} para filtrar por moneda, {date_from} y {date_to} para el periodo.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="parameters" class="form-label">Parámetros adicionales (JSON)</label>
                            <textarea class="form-control" id="parameters" name="parameters" rows="3">{{ old('parameters') }}</textarea>
                            <small class="form-text text-muted">Formato: {"nombre_parametro": {"type": "string", "default": "valor"}}</small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="is_public" name="is_public" {{ old('is_public') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">
                                Reporte Público
                            </label>
                            <small class="form-text text-muted d-block">Si está marcado, el reporte será visible para todos los usuarios.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="schedule" class="form-label">Programación (Cron)</label>
                            <input type="text" class="form-control" id="schedule" name="schedule" value="{{ old('schedule') }}">
                            <small class="form-text text-muted">Expresión cron para programar la ejecución automática (ej: 0 0 * * * para diario a medianoche)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="recipients" class="form-label">Destinatarios (JSON)</label>
                            <textarea class="form-control" id="recipients" name="recipients" rows="2">{{ old('recipients') }}</textarea>
                            <small class="form-text text-muted">Lista de correos electrónicos separados por comas o formato JSON</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Reporte
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar sección de colectores específicos
        const collectorFilterSelect = document.getElementById('collector_filter');
        const collectorSpecificSection = document.querySelector('.collector-specific-section');
        
        collectorFilterSelect.addEventListener('change', function() {
            if (this.value === 'specific') {
                collectorSpecificSection.style.display = 'block';
            } else {
                collectorSpecificSection.style.display = 'none';
            }
        });
        
        // Inicializar estado
        if (collectorFilterSelect.value === 'specific') {
            collectorSpecificSection.style.display = 'block';
        }
    });
</script>
@endpush
@endsection 