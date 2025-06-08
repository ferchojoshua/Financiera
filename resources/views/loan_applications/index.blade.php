@extends('layouts.app')

@section('title', 'Solicitudes de Crédito')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Solicitudes de Crédito</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Solicitudes</li>
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Solicitudes</h3>
            <div class="card-tools">
                <a href="{{ route('loan-applications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Solicitud
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="{{ route('loan-applications.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="client">Cliente</label>
                            <input type="text" class="form-control" id="client" name="client" 
                                   value="{{ request('client') }}" 
                                   placeholder="Nombre o NIT">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Pendiente
                                </option>
                                <option value="in_analysis" {{ request('status') == 'in_analysis' ? 'selected' : '' }}>
                                    En Análisis
                                </option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                    Aprobado
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    Rechazado
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">Fecha Desde</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">Fecha Hasta</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('loan-applications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla de Solicitudes -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Tipo de Crédito</th>
                            <th>Monto</th>
                            <th>Plazo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>{{ $application->id }}</td>
                                <td>
                                    {{ $application->client->name }} {{ $application->client->last_name }}
                                    <br>
                                    <small class="text-muted">{{ $application->client->nit }}</small>
                                </td>
                                <td>{{ $application->creditType->name }}</td>
                                <td>${{ number_format($application->amount_requested, 2) }}</td>
                                <td>{{ $application->term_months }} meses</td>
                                <td>
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
                                </td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('loan-applications.show', $application) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($application->status == 'pending')
                                            <a href="{{ route('loan-applications.edit', $application) }}" 
                                               class="btn btn-warning btn-sm"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(Auth::user()->hasRole(['admin', 'analyst']))
                                                <button type="button" 
                                                        class="btn btn-success btn-sm" 
                                                        title="Aprobar"
                                                        data-toggle="modal" 
                                                        data-target="#approvalModal{{ $application->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        title="Rechazar"
                                                        data-toggle="modal" 
                                                        data-target="#rejectionModal{{ $application->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Modal de Aprobación -->
                                    <div class="modal fade" id="approvalModal{{ $application->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('loan-applications.approve', $application) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Aprobar Solicitud #{{ $application->id }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="notes">Notas de Aprobación</label>
                                                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success">Aprobar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de Rechazo -->
                                    <div class="modal fade" id="rejectionModal{{ $application->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('loan-applications.reject', $application) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Rechazar Solicitud #{{ $application->id }}</h5>
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
                                                        <button type="submit" class="btn btn-danger">Rechazar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay solicitudes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $applications->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccionar...',
        allowClear: true
    });
});
</script>
@endpush 