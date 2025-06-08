@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Listado de Clientes</h3>
                        <div>
                            @if(auth()->user()->hasModuleAccess('clientes'))
                                <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nuevo Cliente
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('clients.index') }}" method="GET" class="form-inline">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Buscar por nombre, NIT, teléfono..." 
                                           value="{{ request('search') }}">
                                    <select name="status" class="form-control ml-2">
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                        <option value="blacklisted" {{ request('status') == 'blacklisted' ? 'selected' : '' }}>Lista Negra</option>
                                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                                    Exportar
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('clients.export', ['format' => 'excel']) }}">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                    <a class="dropdown-item" href="{{ route('clients.export', ['format' => 'pdf']) }}">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Clientes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>NIT</th>
                                    <th>Teléfono</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Créditos Activos</th>
                                    <th>Última Actividad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>
                                            <a href="{{ route('clients.show', $client) }}">
                                                {{ $client->full_name }}
                                            </a>
                                            @if($client->blacklisted)
                                                <span class="badge badge-danger">Lista Negra</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->nit }}</td>
                                        <td>{{ $client->phone }}</td>
                                        <td>
                                            @if($client->clientType)
                                                <span class="badge" style="background-color: {{ $client->clientType->color }}">
                                                    {{ $client->clientType->name }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Sin Tipo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$client->is_active)
                                                <span class="badge badge-danger">Inactivo</span>
                                            @elseif($client->blacklisted)
                                                <span class="badge badge-dark">Lista Negra</span>
                                            @else
                                                <span class="badge badge-success">Activo</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $client->credits()->where('status', 'active')->count() }}
                                        </td>
                                        <td>
                                            {{ $client->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('clients.show', $client) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasModuleAccess('clientes'))
                                                    <a href="{{ route('clients.edit', $client) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(auth()->user()->isAdmin())
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="Eliminar"
                                                            onclick="confirmDelete({{ $client->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No se encontraron clientes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-3">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar este cliente? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(clientId) {
    const form = document.getElementById('deleteForm');
    form.action = `/clients/${clientId}`;
    $('#deleteModal').modal('show');
}
</script>
@endpush 