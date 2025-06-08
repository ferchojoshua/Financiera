@extends('layouts.app')

@section('title', 'Detalle del Cliente')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Tarjeta de Información Principal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información Principal</h3>
                </div>
                <div class="card-body box-profile">
                    <div class="text-center mb-3">
                        <img class="profile-user-img img-fluid img-circle" 
                             src="{{ asset('img/default-user.png') }}" 
                             alt="Foto del cliente">
                    </div>

                    <h3 class="profile-username text-center">{{ $client->full_name }}</h3>
                    
                    @if($client->clientType)
                        <p class="text-muted text-center">
                            <span class="badge" style="background-color: {{ $client->clientType->color }}">
                                {{ $client->clientType->name }}
                            </span>
                        </p>
                    @endif

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>NIT</b> <a class="float-right">{{ $client->nit }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>DUI</b> <a class="float-right">{{ $client->dui ?? 'No registrado' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Teléfono</b> <a class="float-right">{{ $client->phone }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Email</b> <a class="float-right">{{ $client->email ?? 'No registrado' }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Estado</b> 
                            <span class="float-right">
                                @if(!$client->is_active)
                                    <span class="badge badge-danger">Inactivo</span>
                                @elseif($client->blacklisted)
                                    <span class="badge badge-dark">Lista Negra</span>
                                @else
                                    <span class="badge badge-success">Activo</span>
                                @endif
                            </span>
                        </li>
                    </ul>

                    <div class="btn-group w-100">
                        @if(auth()->user()->hasModuleAccess('clientes'))
                            <a href="{{ route('clients.edit', ['id' => $client->id]) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                        
                        @if($client->blacklisted && auth()->user()->isAdmin())
                            <form action="{{ route('clients.reactivate', ['id' => $client->id]) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('¿Está seguro de reactivar este cliente?')">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-check"></i> Reactivar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Agente Asignado -->
            @if($client->assignedAgent)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agente Asignado</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/default-user.png') }}" 
                             alt="Agente" 
                             class="img-circle mr-3" 
                             style="width: 40px;">
                        <div>
                            <h5 class="mb-0">{{ $client->assignedAgent->name }}</h5>
                            <small class="text-muted">
                                <i class="fas fa-phone"></i> {{ $client->assignedAgent->phone }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#details" data-toggle="tab">Detalles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#credits" data-toggle="tab">Créditos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#records" data-toggle="tab">Expediente</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#business" data-toggle="tab">Negocio</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Pestaña de Detalles -->
                        <div class="tab-pane active" id="details">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Información Personal</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Género</th>
                                            <td>{{ $client->gender == 'M' ? 'Masculino' : ($client->gender == 'F' ? 'Femenino' : 'No especificado') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado Civil</th>
                                            <td>{{ ucfirst($client->civil_status ?? 'No especificado') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Nacimiento</th>
                                            <td>{{ $client->birthdate ? $client->birthdate->format('d/m/Y') : 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipo de Vivienda</th>
                                            <td>{{ ucfirst($client->house_type ?? 'No especificado') }}</td>
                                        </tr>
                                    </table>

                                    @if($client->civil_status == 'casado')
                                    <h5 class="mt-4">Información del Cónyuge</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Nombre</th>
                                            <td>{{ $client->spouse_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ocupación</th>
                                            <td>{{ $client->spouse_job ?? 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Teléfono</th>
                                            <td>{{ $client->spouse_phone ?? 'No especificado' }}</td>
                                        </tr>
                                    </table>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <h5>Dirección</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Dirección</th>
                                            <td>{{ $client->address }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ciudad</th>
                                            <td>{{ $client->city ?? 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Departamento</th>
                                            <td>{{ $client->state ?? 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Provincia/Municipio</th>
                                            <td>{{ $client->province ?? 'No especificada' }}</td>
                                        </tr>
                                    </table>

                                    @if($client->lat && $client->lng)
                                    <div id="map" style="height: 200px;" class="mt-3"></div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña de Créditos -->
                        <div class="tab-pane" id="credits">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Historial de Créditos</h5>
                                @if($isEligible)
                                    <a href="{{ route('credits.create.client', ['client_id' => $client->id]) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Nuevo Crédito
                                    </a>
                                @endif
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Monto</th>
                                            <th>Plazo</th>
                                            <th>Estado</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($credits as $credit)
                                            <tr>
                                                <td>{{ $credit->id }}</td>
                                                <td>${{ number_format($credit->amount, 2) }}</td>
                                                <td>{{ $credit->term }} días</td>
                                                <td>
                                                    @switch($credit->status)
                                                        @case('active')
                                                            <span class="badge badge-success">Activo</span>
                                                            @break
                                                        @case('paid')
                                                            <span class="badge badge-info">Pagado</span>
                                                            @break
                                                        @case('late')
                                                            <span class="badge badge-danger">Atrasado</span>
                                                            @break
                                                        @default
                                                            <span class="badge badge-secondary">{{ ucfirst($credit->status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('credits.show', $credit) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No hay créditos registrados</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pestaña de Expediente -->
                        <div class="tab-pane" id="records">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Expediente del Cliente</h5>
                                <button type="button" 
                                        class="btn btn-primary" 
                                        data-toggle="modal" 
                                        data-target="#addRecordModal">
                                    <i class="fas fa-plus"></i> Agregar Nota
                                </button>
                            </div>

                            <div class="timeline">
                                @forelse($client->records()->orderBy('created_at', 'desc')->get() as $record)
                                    <div class="time-label">
                                        <span class="bg-info">
                                            {{ $record->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div>
                                        @switch($record->record_type)
                                            @case('note')
                                                <i class="fas fa-comment bg-info"></i>
                                                @break
                                            @case('document')
                                                <i class="fas fa-file bg-warning"></i>
                                                @break
                                            @case('call')
                                                <i class="fas fa-phone bg-success"></i>
                                                @break
                                            @case('visit')
                                                <i class="fas fa-home bg-primary"></i>
                                                @break
                                            @case('payment')
                                                <i class="fas fa-dollar-sign bg-success"></i>
                                                @break
                                            @case('late')
                                                <i class="fas fa-exclamation-triangle bg-danger"></i>
                                                @break
                                            @case('warning')
                                                <i class="fas fa-exclamation-circle bg-warning"></i>
                                                @break
                                            @case('blacklist')
                                                <i class="fas fa-ban bg-danger"></i>
                                                @break
                                            @default
                                                <i class="fas fa-info bg-info"></i>
                                        @endswitch

                                        <div class="timeline-item">
                                            <span class="time">
                                                <i class="fas fa-clock"></i> 
                                                {{ $record->created_at->format('H:i') }}
                                            </span>
                                            <h3 class="timeline-header">
                                                {{ ucfirst($record->record_type) }} 
                                                por {{ $record->createdBy->name }}
                                            </h3>
                                            <div class="timeline-body">
                                                {{ $record->description }}
                                            </div>
                                            @if($record->media_urls)
                                                <div class="timeline-footer">
                                                    @foreach($record->media_urls as $url)
                                                        <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-download"></i> Adjunto
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center">No hay registros en el expediente</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Pestaña de Negocio -->
                        <div class="tab-pane" id="business">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Información del Negocio</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Nombre del Negocio</th>
                                            <td>{{ $client->business_name ?? 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sector</th>
                                            <td>{{ $client->business_sector ?? 'No especificado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Actividad Económica</th>
                                            <td>{{ $client->economic_activity ?? 'No especificada' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ingresos Anuales</th>
                                            <td>${{ number_format($client->annual_revenue ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Empleados</th>
                                            <td>{{ $client->employee_count ?? 'No especificado' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Rendimiento del Negocio</h5>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Ventas Buenas</th>
                                            <td>${{ number_format($client->sales_good ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ventas Malas</th>
                                            <td>${{ number_format($client->sales_bad ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Promedio Semanal</th>
                                            <td>${{ number_format($client->weekly_average ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Utilidad Neta</th>
                                            <td>${{ number_format($client->net_profit ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Nota al Expediente -->
<div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('clients.records.add', ['id' => $client->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nota al Expediente</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="record_type">Tipo de Registro</label>
                        <select class="form-control" id="record_type" name="record_type" required>
                            <option value="note">Nota General</option>
                            <option value="call">Llamada</option>
                            <option value="visit">Visita</option>
                            <option value="warning">Advertencia</option>
                            <option value="document">Documento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="media">Archivos Adjuntos</label>
                        <input type="file" class="form-control-file" id="media" name="media[]" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($client->lat && $client->lng)
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
<script>
function initMap() {
    const location = {
        lat: {{ $client->lat }},
        lng: {{ $client->lng }}
    };
    
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
    });
    
    new google.maps.Marker({
        position: location,
        map: map,
        title: '{{ $client->full_name }}'
    });
}

document.addEventListener('DOMContentLoaded', initMap);
</script>
@endif
@endpush 