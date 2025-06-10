@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card"></i> Gestión de Créditos
                        </h4>
                        <div>
                            <a href="{{ route('credit.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle"></i> Nuevo Crédito
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="mb-4">
                        <form action="{{ route('credit.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="searchInput" class="form-label">Buscar:</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" id="searchInput" 
                                       placeholder="Cliente, monto..."
                                       value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="statusFilter" class="form-label">Estado:</label>
                                <select class="form-control" name="status" id="statusFilter">
                                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completados</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>En mora</option>
                                </select>
                            </div>
                            
                            <div class="col-auto">
                                <a href="{{ route('credit.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-sync-alt"></i> Reiniciar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de créditos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Monto + Interés</th>
                                    <th>Cuotas</th>
                                    <th>Frecuencia</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                <tr>
                                    <td>{{ $credit->id }}</td>
                                    <td>
                                        <strong>{{ $credit->client->name }} {{ $credit->client->last_name }}</strong>
                                        <br>
                                        <small class="text-secondary">{{ $credit->client->nit }}</small>
                                    </td>
                                    <td class="text-right">${{ number_format($credit->amount, 2) }}</td>
                                    <td class="text-right">${{ number_format($credit->total_amount, 2) }}</td>
                                    <td>{{ $credit->payment_number }}</td>
                                    <td>{{ ucfirst($credit->frequency) }}</td>
                                    <td>
                                        @if($credit->status == 'completed')
                                            <span class="badge bg-success text-white">Completado</span>
                                        @elseif($credit->status == 'overdue')
                                            <span class="badge bg-danger text-white">En Mora</span>
                                        @else
                                            <span class="badge bg-info text-white">En Progreso</span>
                                        @endif
                                    </td>
                                    <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('credit.show', $credit->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($credit->status != 'completed')
                                            <a href="{{ route('credit.edit', $credit->id) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if($credit->payments_count == 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $credit->id }})"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox empty-icon"></i>
                                            <p>No se encontraron créditos</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-secondary">
                            Mostrando {{ $credits->firstItem() ?? 0 }} - {{ $credits->lastItem() ?? 0 }} de {{ $credits->total() ?? 0 }} registros
                        </div>
                        {{ $credits->links() }}
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
                <p>¿Está seguro que desea eliminar este crédito? Esta acción no se puede deshacer.</p>
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

@push('scripts')
<script>
function confirmDelete(creditId) {
    const form = document.getElementById('deleteForm');
    form.action = `/credits/${creditId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush

@push('styles')
<style>
    /* Solo aplicar estos estilos cuando el body tenga la clase dark-theme */
    body.dark-theme .table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: #343a40;
        color: #f8f9fa;
    }
    
    body.dark-theme .table-hover>tbody>tr:hover>* {
        background-color: #495057;
        color: #f8f9fa;
    }
    
    /* Aseguramos que las filas pares también se vean bien en modo oscuro */
    body.dark-theme .table-striped>tbody>tr:nth-of-type(even)>* {
        background-color: #212529; /* Un poco más oscuro que las impares para mantener el efecto */
        color: #f8f9fa;
    }
</style>
@endpush

@endsection 