@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Listado de Clientes</h4>
                        <a href="{{ route('clients.create') }}" class="btn btn-success">
                            <i class="fas fa-user-plus"></i> Nuevo Cliente
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="mb-4">
                        <form action="{{ route('clients.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Buscar:</label>
                                <div class="input-group">
                                    <input type="text" 
                                       id="search"
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Nombre, NIT, teléfono..."
                                       value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="status" class="form-label">Estado:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            
                            <div class="col-auto">
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync-alt"></i> Reiniciar
                                </a>
                            </div>

                            <div class="col-auto">
                                <div class="dropdown">
                                    <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fas fa-file-export"></i> Exportar
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="{{ route('clients.export', ['format' => 'excel']) }}">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </a>
                                        <a class="dropdown-item" href="{{ route('clients.export', ['format' => 'pdf']) }}">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="table table-striped table-hover">
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
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td>{{ $client->full_name }}</td>
                                    <td>{{ $client->nit }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>{{ $client->type }}</td>
                                    <td>
                                        <span class="badge {{ $client->status == 'active' ? 'bg-success' : 'bg-danger' }} text-white">
                                            {{ $client->status == 'active' ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>{{ $client->active_credits_count }}</td>
                                    <td>{{ $client->last_activity ? $client->last_activity->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Eliminar"
                                                    onclick="confirmDelete('{{ $client->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-{{ $client->id }}" 
                                              action="{{ route('clients.destroy', $client) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="empty-state d-flex flex-column align-items-center justify-content-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-2"></i>
                                            <p class="mb-0 text-muted">No se encontraron clientes</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(clientId) {
    if (confirm('¿Está seguro que desea eliminar este cliente?')) {
        document.getElementById('delete-form-' + clientId).submit();
    }
}
</script>
@endpush
@endsection 