@extends('layouts.app')

@section('title', 'Detalles de Solicitud')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalles de Solicitud #{{ $application->id }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('loan-applications.index') }}">Solicitudes</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Información Principal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información de la Solicitud</h3>
                    <div class="card-tools">
                        @if($application->status == 'pending')
                            <a href="{{ route('loan-applications.edit', $application) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Cliente</dt>
                                <dd>{{ $application->client->name }} {{ $application->client->last_name }}</dd>
                                
                                <dt>Tipo de Crédito</dt>
                                <dd>{{ $application->creditType->name }}</dd>
                                
                                <dt>Monto Solicitado</dt>
                                <dd>${{ number_format($application->amount_requested, 2) }}</dd>
                                
                                <dt>Plazo</dt>
                                <dd>{{ $application->term_months }} meses</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Frecuencia de Pago</dt>
                                <dd>
                                    @switch($application->payment_frequency)
                                        @case('daily')
                                            Diario
                                            @break
                                        @case('weekly')
                                            Semanal
                                            @break
                                        @case('biweekly')
                                            Quincenal
                                            @break
                                        @case('monthly')
                                            Mensual
                                            @break
                                    @endswitch
                                </dd>
                                
                                <dt>Estado</dt>
                                <dd>
                                    @switch($application->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Pendiente</span>
                                            @break
                                        @case('in_analysis')
                                            <span class="badge badge-info">En Análisis</span>
                                            @break
                                        @case('approved')
                                            <span class="badge badge-success">Aprobado</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge badge-danger">Rechazado</span>
                                            @break
                                    @endswitch
                                </dd>
                                
                                <dt>Fecha de Solicitud</dt>
                                <dd>{{ $application->created_at->format('d/m/Y H:i') }}</dd>
                                
                                <dt>Analista Asignado</dt>
                                <dd>{{ $application->analyst->name ?? 'Sin asignar' }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($application->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Notas/Observaciones</h5>
                                <p class="text-muted">{{ $application->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Historial de Cambios -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Historial de la Solicitud</h3>
                </div>
                <div class="card-body p-0">
                    <div class="timeline timeline-inverse">
                        <!-- Creación -->
                        <div>
                            <i class="fas fa-file bg-primary"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="far fa-clock"></i> 
                                    {{ $application->created_at->format('d/m/Y H:i') }}
                                </span>
                                <h3 class="timeline-header">
                                    Solicitud creada por {{ $application->createdBy->name }}
                                </h3>
                            </div>
                        </div>

                        <!-- Aprobación/Rechazo -->
                        @if($application->status == 'approved' || $application->status == 'rejected')
                            <div>
                                <i class="fas fa-{{ $application->status == 'approved' ? 'check' : 'times' }} 
                                   bg-{{ $application->status == 'approved' ? 'success' : 'danger' }}"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="far fa-clock"></i> 
                                        {{ $application->approval_date->format('d/m/Y H:i') }}
                                    </span>
                                    <h3 class="timeline-header">
                                        Solicitud {{ $application->status == 'approved' ? 'aprobada' : 'rechazada' }} 
                                        por {{ $application->approvedBy->name }}
                                    </h3>
                                    @if($application->rejection_reason)
                                        <div class="timeline-body">
                                            Razón: {{ $application->rejection_reason }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Acciones</h3>
                </div>
                <div class="card-body">
                    @if($application->status == 'pending')
                        @if(Auth::user()->hasRole(['admin', 'analyst']))
                            <button type="button" class="btn btn-success btn-block mb-3" data-toggle="modal" data-target="#approvalModal">
                                <i class="fas fa-check"></i> Aprobar Solicitud
                            </button>
                            <button type="button" class="btn btn-danger btn-block mb-3" data-toggle="modal" data-target="#rejectionModal">
                                <i class="fas fa-times"></i> Rechazar Solicitud
                            </button>
                        @endif
                        
                        @if(Auth::user()->hasRole('admin') || Auth::user()->id == $application->created_by)
                            <form action="{{ route('loan-applications.destroy', $application) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Está seguro de eliminar esta solicitud?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-block">
                                    <i class="fas fa-trash"></i> Eliminar Solicitud
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Cliente</h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Nombre Completo</dt>
                        <dd>{{ $application->client->name }} {{ $application->client->last_name }}</dd>
                        
                        <dt>NIT/Cédula</dt>
                        <dd>{{ $application->client->nit }}</dd>
                        
                        <dt>Teléfono</dt>
                        <dd>{{ $application->client->phone }}</dd>
                        
                        <dt>Dirección</dt>
                        <dd>{{ $application->client->address }}</dd>
                    </dl>
                    
                    <a href="{{ route('clients.show', $application->client) }}" class="btn btn-info btn-block">
                        <i class="fas fa-user"></i> Ver Perfil del Cliente
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprobación -->
<div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-applications.approve', $application) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Aprobar Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes">Notas de Aprobación</label>
                        <textarea class="form-control" id="approval_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('loan-applications.reject', $application) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Rechazar Solicitud</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Razón del Rechazo *</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 