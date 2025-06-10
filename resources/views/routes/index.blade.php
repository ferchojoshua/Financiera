@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold">Gestión de Rutas</h4>
                        <a href="{{ route('routes.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-plus"></i> Nueva Ruta
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-left-4 shadow-sm">
                        <i class="fa fa-info-circle mr-2"></i> Administre las rutas de cobranza y asigne préstamos a cada ruta.
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('routes.index') }}" class="form-inline flex-wrap">
                                <div class="form-group mr-2 mb-2">
                                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group mr-2 mb-2">
                                    <select name="collector_id" class="form-control form-control-sm">
                                        <option value="">Todos los cobradores</option>
                                        @foreach($collectors as $collector)
                                            <option value="{{ $collector->id }}" {{ request('collector_id') == $collector->id ? 'selected' : '' }}>
                                                {{ $collector->name }} {{ $collector->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-2 mb-2">
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">Todos los estados</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activas</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivas</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mb-2 mr-2">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('routes.index') }}" class="btn btn-secondary btn-sm mb-2">
                                    <i class="fa fa-sync"></i> Reiniciar
                                </a>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover border">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Zona</th>
                                    <th>Cobrador</th>
                                    <th>Supervisor</th>
                                    <th>Días</th>
                                    <th>Estado</th>
                                    <th>Préstamos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                    <tr>
                                        <td>{{ $route->id }}</td>
                                        <td><strong>{{ $route->name }}</strong></td>
                                        <td>{{ $route->description }}</td>
                                        <td>{{ $route->zone }}</td>
                                        <td>
                                            @if($route->collector)
                                                <a href="{{ route('config.users.edit', $route->collector_id) }}" class="text-primary">
                                                    {{ $route->collector->name }} {{ $route->collector->last_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">No asignado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($route->supervisor)
                                                <a href="{{ route('config.users.edit', $route->supervisor->id) }}" class="text-primary">
                                                    {{ $route->supervisor->name }} {{ $route->supervisor->last_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">No asignado</span>
                                            @endif
                                        </td>
                                        <td>{{ $route->days_formatted }}</td>
                                        <td>
                                            @if($route->status == 'active')
                                                <span class="badge bg-success text-white">Activa</span>
                                            @else
                                                <span class="badge bg-danger text-white">Inactiva</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary text-white">
                                                {{ $route->credits()->where('status', 'active')->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('routes.show', $route->id) }}" class="btn btn-sm btn-info text-white" title="Ver detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-sm btn-warning text-white" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('routes.assign_credits', $route->id) }}" class="btn btn-sm btn-primary text-white" title="Asignar créditos">
                                                    <i class="fa fa-tasks"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger text-white" 
                                                        onclick="confirmDelete('{{ $route->id }}', '{{ $route->name }}')" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <form id="delete-form-{{ $route->id }}" action="{{ route('routes.destroy', $route->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4 text-muted">
                                            <i class="fa fa-exclamation-circle fa-2x mb-2"></i>
                                            <p>No hay rutas disponibles</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $routes->appends(request()->except('page'))->links() }}
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="card shadow-sm">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0 font-weight-bold">Distribución de Préstamos por Ruta</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="routeDistributionChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0 font-weight-bold">Resumen de Rutas</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Total de rutas:</strong>
                                            <span class="badge bg-primary text-white rounded-pill">{{ $routeCount ?? $routes->total() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Rutas activas:</strong>
                                            <span class="badge bg-success text-white rounded-pill">{{ $activeRouteCount ?? $routes->where('status', 'active')->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Rutas inactivas:</strong>
                                            <span class="badge bg-danger text-white rounded-pill">{{ $inactiveRouteCount ?? $routes->where('status', 'inactive')->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Préstamos asignados:</strong>
                                            <span class="badge bg-info text-white rounded-pill">
                                                {{ $totalAssignedCredits ?? 0 }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar la ruta <strong id="routeName"></strong>?
                <p class="text-danger mt-2">Esta acción no se puede deshacer y podría afectar a los préstamos asignados.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Configuración del gráfico si hay datos
        @if(isset($routes) && $routes->count() > 0)
        var ctx = document.getElementById('routeDistributionChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($routes as $route)
                        '{{ $route->name }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($routes as $route)
                            {{ $route->credits()->where('status', 'active')->count() }},
                        @endforeach
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Préstamos por Ruta',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
        @endif
    });

    // Función para confirmar eliminación
    function confirmDelete(routeId, routeName) {
        $('#routeName').text(routeName);
        $('#deleteModal').modal('show');
        
        $('#confirmDelete').off('click').on('click', function() {
            $('#delete-form-' + routeId).submit();
        });
    }
</script>
@endsection 