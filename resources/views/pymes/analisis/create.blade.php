@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Análisis de Crédito</h4>
                    <div>
                        <a href="{{ route('pymes.solicitudes.show', $solicitud->id) }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> Volver a Solicitud
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información de la Solicitud -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Información del Cliente</h5>
                                @if($solicitud->client->business_name)
                                    <p><strong>Empresa:</strong> {{ $solicitud->client->business_name }}</p>
                                    <p><strong>CED/RUC:</strong> {{ $solicitud->client->tax_id }}</p>
                                    <p><strong>Representante:</strong> {{ $solicitud->client->name }}</p>
                                @else
                                    <p><strong>Nombre:</strong> {{ $solicitud->client->name }}</p>
                                    <p><strong>Identificación:</strong> {{ $solicitud->client->tax_id }}</p>
                                @endif
                                <p><strong>Teléfono:</strong> {{ $solicitud->client->phone }}</p>
                                <p><strong>Email:</strong> {{ $solicitud->client->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Detalles del Crédito</h5>
                                <p><strong>Monto Solicitado:</strong> ${{ number_format($solicitud->amount_requested, 2) }}</p>
                                <p><strong>Plazo:</strong> {{ $solicitud->term_months }} meses</p>
                                <p><strong>Tipo de Crédito:</strong> {{ $solicitud->loan_type }}</p>
                                <p><strong>Fecha Solicitud:</strong> {{ $solicitud->application_date ? $solicitud->application_date->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Análisis -->
                    <form method="POST" action="{{ route('pymes.analisis.store') }}" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="loan_application_id" value="{{ $solicitud->id }}">

                        <!-- Estados Financieros o Documentación de Ingresos -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ $solicitud->client->business_name ? 'Estados Financieros' : 'Documentación de Ingresos' }}</h5>
                            </div>
                            <div class="card-body">
                                @if($solicitud->financialStatements->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Período</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($solicitud->financialStatements as $statement)
                                                    <tr>
                                                        <td>{{ $statement->statement_type }}</td>
                                                        <td>{{ $statement->period_start->format('d/m/Y') }} - {{ $statement->period_end->format('d/m/Y') }}</td>
                                                        <td>
                                                            @if($statement->is_validated)
                                                                <span class="badge bg-success">Validado</span>
                                                            @else
                                                                <span class="badge bg-warning">Pendiente</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-info"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#statementModal{{ $statement->id }}">
                                                                <i class="fas fa-eye"></i> Ver
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        No hay documentos registrados.
                                        <button type="button" 
                                                class="btn btn-sm btn-warning ms-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#newStatementModal">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Análisis Cualitativo -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Análisis Cualitativo</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($solicitud->client->business_name)
                                    <div class="col-md-6 mb-3">
                                        <label for="management_experience" class="form-label">Experiencia Gerencial</label>
                                        <select class="form-select" id="management_experience" name="qualitative_factors[management_experience]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="excellent">Excelente (>10 años)</option>
                                            <option value="good">Buena (5-10 años)</option>
                                            <option value="average">Regular (2-5 años)</option>
                                            <option value="limited">Limitada (<2 años)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="market_position" class="form-label">Posición en el Mercado</label>
                                        <select class="form-select" id="market_position" name="qualitative_factors[market_position]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="leader">Líder</option>
                                            <option value="strong">Fuerte</option>
                                            <option value="average">Promedio</option>
                                            <option value="weak">Débil</option>
                                        </select>
                                    </div>
                                    @else
                                    <div class="col-md-6 mb-3">
                                        <label for="employment_stability" class="form-label">Estabilidad Laboral</label>
                                        <select class="form-select" id="employment_stability" name="qualitative_factors[employment_stability]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="excellent">Excelente (>5 años)</option>
                                            <option value="good">Buena (3-5 años)</option>
                                            <option value="average">Regular (1-3 años)</option>
                                            <option value="limited">Limitada (<1 año)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="income_stability" class="form-label">Estabilidad de Ingresos</label>
                                        <select class="form-select" id="income_stability" name="qualitative_factors[income_stability]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="very_stable">Muy Estable</option>
                                            <option value="stable">Estable</option>
                                            <option value="variable">Variable</option>
                                            <option value="unstable">Inestable</option>
                                        </select>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="credit_history" class="form-label">Historial Crediticio</label>
                                        <select class="form-select" id="credit_history" name="qualitative_factors[credit_history]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="excellent">Excelente</option>
                                            <option value="good">Bueno</option>
                                            <option value="fair">Regular</option>
                                            <option value="poor">Deficiente</option>
                                            <option value="none">Sin historial</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="payment_capacity" class="form-label">Capacidad de Pago</label>
                                        <select class="form-select" id="payment_capacity" name="qualitative_factors[payment_capacity]" required>
                                            <option value="">Seleccionar...</option>
                                            <option value="excellent">Excelente</option>
                                            <option value="good">Buena</option>
                                            <option value="moderate">Moderada</option>
                                            <option value="limited">Limitada</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="qualitative_notes" class="form-label">Notas y Observaciones</label>
                                    <textarea class="form-control" id="qualitative_notes" name="qualitative_notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Scoring y Recomendación -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Scoring y Recomendación</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="risk_level" class="form-label">Nivel de Riesgo</label>
                                        <select class="form-select @error('risk_level') is-invalid @enderror" 
                                                id="risk_level" 
                                                name="risk_level" 
                                                required>
                                            <option value="">Seleccionar...</option>
                                            <option value="very_low" {{ old('risk_level') == 'very_low' ? 'selected' : '' }}>Muy Bajo</option>
                                            <option value="low" {{ old('risk_level') == 'low' ? 'selected' : '' }}>Bajo</option>
                                            <option value="medium" {{ old('risk_level') == 'medium' ? 'selected' : '' }}>Medio</option>
                                            <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>Alto</option>
                                            <option value="very_high" {{ old('risk_level') == 'very_high' ? 'selected' : '' }}>Muy Alto</option>
                                        </select>
                                        @error('risk_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="recommendation" class="form-label">Recomendación</label>
                                        <select class="form-select @error('recommendation') is-invalid @enderror" 
                                                id="recommendation" 
                                                name="recommendation" 
                                                required>
                                            <option value="">Seleccionar...</option>
                                            <option value="approve" {{ old('recommendation') == 'approve' ? 'selected' : '' }}>Aprobar</option>
                                            <option value="reject" {{ old('recommendation') == 'reject' ? 'selected' : '' }}>Rechazar</option>
                                            <option value="review" {{ old('recommendation') == 'review' ? 'selected' : '' }}>Revisar</option>
                                        </select>
                                        @error('recommendation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas y Observaciones</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pymes.solicitudes.show', $solicitud->id) }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Guardar Análisis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Estado Financiero -->
<div class="modal fade" id="newStatementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pymes.analisis.store-statement') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="loan_application_id" value="{{ $solicitud->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Estado Financiero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="statement_type" class="form-label">Tipo de Estado Financiero</label>
                        <select class="form-select" id="statement_type" name="statement_type" required>
                            <option value="">Seleccionar...</option>
                            <option value="balance">Balance General</option>
                            <option value="income">Estado de Resultados</option>
                            <option value="cashflow">Flujo de Caja</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="period_start" class="form-label">Fecha Inicio del Período</label>
                        <input type="date" class="form-control" id="period_start" name="period_start" required>
                    </div>

                    <div class="mb-3">
                        <label for="period_end" class="form-label">Fecha Fin del Período</label>
                        <input type="date" class="form-control" id="period_end" name="period_end" required>
                    </div>

                    <div class="mb-3">
                        <label for="statement_file" class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="statement_file" name="statement_file" required>
                        <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX. Máximo 10MB.</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales para Ver Estados Financieros -->
@foreach($solicitud->financialStatements as $statement)
    <div class="modal fade" id="statementModal{{ $statement->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Estado Financiero</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <dt class="col-sm-4">Tipo:</dt>
                        <dd class="col-sm-8">{{ $statement->statement_type }}</dd>
                        
                        <dt class="col-sm-4">Período:</dt>
                        <dd class="col-sm-8">
                            {{ $statement->period_start->format('d/m/Y') }} - 
                            {{ $statement->period_end->format('d/m/Y') }}
                        </dd>
                        
                        <dt class="col-sm-4">Estado:</dt>
                        <dd class="col-sm-8">
                            @if($statement->is_validated)
                                <span class="badge bg-success">Validado</span>
                            @else
                                <span class="badge bg-warning">Pendiente</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-4">Archivo:</dt>
                        <dd class="col-sm-8">
                            <a href="{{ Storage::url($statement->file_path) }}" 
                               class="btn btn-sm btn-info" 
                               target="_blank">
                                <i class="fas fa-download"></i> Descargar
                            </a>
                        </dd>
                        
                        @if($statement->notes)
                            <dt class="col-sm-4">Notas:</dt>
                            <dd class="col-sm-8">{{ $statement->notes }}</dd>
                        @endif
                    </dl>

                    @if(!$statement->is_validated)
                        <form action="{{ route('pymes.analisis.validate-statement', $statement->id) }}" 
                              method="POST" 
                              class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Validar Estado Financiero
                            </button>
                        </form>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Validación de fechas
    var startDate = document.getElementById('period_start');
    var endDate = document.getElementById('period_end');
    
    if(startDate && endDate) {
        endDate.addEventListener('change', function() {
            if(startDate.value && this.value) {
                if(new Date(this.value) <= new Date(startDate.value)) {
                    this.setCustomValidity('La fecha final debe ser posterior a la fecha inicial');
                } else {
                    this.setCustomValidity('');
                }
            }
        });
    }
});
</script>
@endpush 