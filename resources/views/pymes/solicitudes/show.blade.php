@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Mensajes de error y éxito -->
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

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles de la Solicitud</h4>
                    <div>
                        <a href="{{ route('pymes.solicitudes') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información Principal -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Información del Cliente</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Empresa:</dt>
                                <dd class="col-sm-8">{{ $solicitud->client->business_name ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">NIT:</dt>
                                <dd class="col-sm-8">{{ $solicitud->client->tax_id ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Contacto:</dt>
                                <dd class="col-sm-8">{{ $solicitud->client->name ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Email:</dt>
                                <dd class="col-sm-8">{{ $solicitud->client->email ?? 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Teléfono:</dt>
                                <dd class="col-sm-8">{{ $solicitud->client->phone ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Detalles del Crédito</h5>
                            <dl class="row">
                                <dt class="col-sm-4">Monto Solicitado:</dt>
                                <dd class="col-sm-8">${{ number_format($solicitud->amount_requested, 2) }}</dd>
                                
                                <dt class="col-sm-4">Plazo:</dt>
                                <dd class="col-sm-8">{{ $solicitud->term_months }} meses</dd>
                                
                                <dt class="col-sm-4">Tipo de Crédito:</dt>
                                <dd class="col-sm-8">{{ $solicitud->loan_type }}</dd>
                                
                                <dt class="col-sm-4">Fecha Solicitud:</dt>
                                <dd class="col-sm-8">{{ $solicitud->application_date ? $solicitud->application_date->format('d/m/Y') : 'N/A' }}</dd>
                                
                                <dt class="col-sm-4">Estado:</dt>
                                <dd class="col-sm-8">
                                    @switch($solicitud->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pendiente</span>
                                            @break
                                        @case('under_review')
                                            <span class="badge bg-info">En revisión</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">Aprobado</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Rechazado</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $solicitud->status }}</span>
                                    @endswitch
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Documentos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Documentos Adjuntos</h5>
                                    <button type="button" 
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#uploadDocumentModal">
                                        <i class="fas fa-plus"></i> Agregar Documento
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if($solicitud->documents->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Tipo</th>
                                                        <th>Fecha de Carga</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($solicitud->documents as $document)
                                                        <tr>
                                                            <td>{{ $document->name }}</td>
                                                            <td>{{ $document->document_type }}</td>
                                                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <a href="{{ Storage::url($document->file_path) }}" 
                                                                   class="btn btn-sm btn-info" 
                                                                   target="_blank">
                                                                    <i class="fas fa-download"></i> Descargar
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No hay documentos adjuntos a esta solicitud.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    @if($solicitud->notes)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2">Notas</h5>
                            <div class="card">
                                <div class="card-body bg-light">
                                    {{ $solicitud->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                @if($solicitud->status === 'pending')
                                    <a href="{{ route('pymes.analisis.create', $solicitud->id) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-chart-line"></i> Realizar Análisis
                                    </a>
                                @endif
                                
                                @if(in_array($solicitud->status, ['pending', 'under_review']) && (auth()->user()->role === 'superadmin' || auth()->user()->level === 'admin' || auth()->user()->hasPermission('approve-loans')))
                                    <button type="button" 
                                            class="btn btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#approveModal">
                                        <i class="fas fa-check"></i> Aprobar
                                    </button>
                                    
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal">
                                        <i class="fas fa-times"></i> Rechazar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprobación -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" action="{{ route('pymes.solicitudes.approve', $solicitud->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Aprobar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Información de la solicitud:</strong><br>
                        Cliente: {{ $solicitud->client->business_name ?? $solicitud->client->name ?? 'N/A' }}<br>
                        Monto: ${{ number_format($solicitud->amount_requested, 2) }}<br>
                        Plazo: {{ $solicitud->term_months }} meses
                    </div>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notas de Aprobación</label>
                        <textarea class="form-control @error('approval_notes') is-invalid @enderror" 
                                  id="approval_notes" 
                                  name="approval_notes" 
                                  rows="3">{{ old('approval_notes') }}</textarea>
                        @error('approval_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmar Aprobación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" action="{{ route('pymes.solicitudes.reject', $solicitud->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Rechazar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Motivo del Rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                  id="rejection_reason" 
                                  name="rejection_reason" 
                                  rows="3" 
                                  required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Cargar Documento -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('solicitudes.upload-document', $solicitud->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Cargar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            <option value="">Seleccionar...</option>
                            <option value="identificacion">Identificación</option>
                            <option value="comprobante_ingresos">Comprobante de Ingresos</option>
                            <option value="estados_financieros">Estados Financieros</option>
                            <option value="declaracion_impuestos">Declaración de Impuestos</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document_name" class="form-label">Nombre del Documento</label>
                        <input type="text" class="form-control" id="document_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="document_file" name="file" required>
                        <div class="form-text">Formatos permitidos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Máximo 10MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Subir Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que Bootstrap está disponible
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap no está cargado');
        return;
    }

    // Debug para verificar que los modales están funcionando
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            console.log('Botón clickeado:', this.getAttribute('data-bs-target'));
        });
    });

    // Inicializar los modales
    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadDocumentModal'));

    // Función para manejar errores de respuesta
    function handleResponseError(response) {
        if (!response.ok) {
            return response.json().then(json => {
                throw new Error(json.message || 'Error en la solicitud');
            });
        }
        return response.json();
    }

    // Función para mostrar mensajes de error
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.container').insertBefore(errorDiv, document.querySelector('.container').firstChild);
    }

    // Función para enviar formulario
    function submitForm(form, modal) {
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(handleResponseError)
        .then(data => {
            if (data.success) {
                modal.hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                throw new Error(data.message || 'Error al procesar la solicitud');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError(error.message);
        });
    }

    // Manejar formulario de aprobación
    const approveForm = document.getElementById('approveForm');
    if (approveForm) {
        approveForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitForm(this, approveModal);
        });
    }

    // Manejar formulario de rechazo
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const rejectionReason = this.querySelector('#rejection_reason').value.trim();
            if (!rejectionReason) {
                showError('Por favor, ingrese el motivo del rechazo');
                return;
            }
            
            submitForm(this, rejectModal);
        });
    }
});
</script>
@endpush 