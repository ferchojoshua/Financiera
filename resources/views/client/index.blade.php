@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white">
                            <i class="fas fa-users"></i> Listado de Clientes
                                        </h4>
                        <a href="{{ route('client.create') }}" class="btn btn-light">
                            <i class="fas fa-plus-circle"></i> Nuevo Cliente
                                        </a>
                                    </div>
                                </div>

                <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Buscar por nombre, NIT, teléfono...">
                                <select class="form-select" id="statusFilter" style="max-width: 150px;">
                                    <option value="active">Activos</option>
                                    <option value="inactive">Inactivos</option>
                                    <option value="all">Todos</option>
                                </select>
                                <button class="btn btn-primary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-secondary" id="exportButton">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                                    </div>
                                </div>

                                <!-- Tabla de clientes -->
                                <div class="table-responsive">
                        <table class="table grid-table">
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
                                    <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @forelse($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td>{{ $client->name }} {{ $client->last_name }}</td>
                                    <td>{{ $client->nit }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $client->type->name ?? 'No definido' }}</span>
                                    </td>
                                    <td>
                                        @if(!$client->is_active)
                                            <span class="status-badge status-inactive">Inactivo</span>
                                        @elseif($client->blacklisted)
                                            <span class="status-badge status-pending">Lista Negra</span>
                                                        @else
                                            <span class="status-badge status-active">Activo</span>
                                                        @endif
                                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $client->active_credits_count ?? 0 }}</span>
                                    </td>
                                    <td>{{ $client->last_activity ? $client->last_activity->format('d/m/Y H:i') : 'Sin actividad' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('client.show', $client->id) }}" 
                                               class="btn btn-view" 
                                               title="Ver detalles">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                            <a href="{{ route('client.edit', $client->id) }}" 
                                               class="btn btn-edit"
                                               title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                            @if($client->active_credits_count == 0)
                                            <button type="button" 
                                                    class="btn btn-delete" 
                                                    onclick="confirmDelete({{ $client->id }})"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No se encontraron clientes</p>
                                    </td>
                                </tr>
                                @endforelse
                                        </tbody>
                                    </table>
                                </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $clients->firstItem() ?? 0 }} - {{ $clients->lastItem() ?? 0 }} de {{ $clients->total() ?? 0 }} registros
                        </div>
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este cliente? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('css/components.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script>
function confirmDelete(clientId) {
    const form = document.getElementById('deleteForm');
    form.action = `/clients/${clientId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Implementar búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const searchButton = document.getElementById('searchButton');

    function performSearch() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        window.location.href = `{{ route('client.index') }}?search=${searchTerm}&status=${status}`;
    }

    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    statusFilter.addEventListener('change', performSearch);

    // Implementar exportación
    document.getElementById('exportButton').addEventListener('click', function() {
        const searchTerm = searchInput.value;
        const status = statusFilter.value;
        window.location.href = `{{ route('client.export') }}?search=${searchTerm}&status=${status}`;
    });
});
</script>
@endpush
@endsection
