@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Gastos</h4>
                    <div>
                        <button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#nuevo-gasto-modal">
                            <i class="fa fa-plus"></i> Nuevo Gasto
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resumen de Gastos -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Total Gastos</h5>
                                    <h3 class="card-text">{{ number_format($total_gastos ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Gastos Hoy</h5>
                                    <h3 class="card-text">{{ number_format($gastos_hoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Este Mes</h5>
                                    <h3 class="card-text">{{ number_format($gastos_mes ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Presupuesto</h5>
                                    <h3 class="card-text">{{ number_format($presupuesto ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <form action="{{ route('gastos.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_desde">Desde:</label>
                                    <input type="text" class="form-control datepicker" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde', date('d/m/Y', strtotime('-30 days'))) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_hasta">Hasta:</label>
                                    <input type="text" class="form-control datepicker" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta', date('d/m/Y')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="categoria">Categoría:</label>
                                    <select name="categoria" id="categoria" class="form-control">
                                        <option value="">Todas las categorías</option>
                                        <option value="oficina">Material de Oficina</option>
                                        <option value="servicios">Servicios</option>
                                        <option value="nomina">Nómina</option>
                                        <option value="impuestos">Impuestos</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('gastos.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-undo"></i> Reiniciar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de Gastos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Comprobante</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastos ?? [] as $gasto)
                                    <tr>
                                        <td>{{ $gasto->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $gasto->descripcion }}</td>
                                        <td>
                                            <span class="badge bg-{{ $gasto->categoria == 'nomina' ? 'primary' : ($gasto->categoria == 'servicios' ? 'info' : ($gasto->categoria == 'impuestos' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst($gasto->categoria) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($gasto->monto, 2) }}</td>
                                        <td>
                                            @if($gasto->comprobante)
                                                <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#comprobante-modal-{{ $gasto->id }}">
                                                    <i class="fa fa-file-invoice"></i> Ver
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">Sin comprobante</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editar-gasto-modal-{{ $gasto->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#eliminar-gasto-modal-{{ $gasto->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay gastos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $gastos->links() ?? '' }}
                    </div>

                    <!-- Gráficos -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Gastos por Categoría</h5>
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="gastosCategoriaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">Gastos por Mes</h5>
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="gastosMensualChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuevo Gasto -->
<div class="modal fade" id="nuevo-gasto-modal" tabindex="-1" role="dialog" aria-labelledby="nuevoGastoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="nuevoGastoModalLabel">Registrar Nuevo Gasto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('gastos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="fecha">Fecha:</label>
                        <input type="text" class="form-control datepicker" id="fecha" name="fecha" value="{{ date('d/m/Y') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría:</label>
                        <select name="categoria" id="categoria" class="form-control" required>
                            <option value="">Seleccione una categoría</option>
                            <option value="oficina">Material de Oficina</option>
                            <option value="servicios">Servicios</option>
                            <option value="nomina">Nómina</option>
                            <option value="impuestos">Impuestos</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="monto">Monto:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="monto" name="monto" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comprobante">Comprobante (opcional):</label>
                        <input type="file" class="form-control-file" id="comprobante" name="comprobante">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-save"></i> Guardar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
        
        // Gráfico de Gastos por Categoría
        var categoriaCtx = document.getElementById('gastosCategoriaChart').getContext('2d');
        var categoriaChart = new Chart(categoriaCtx, {
            type: 'pie',
            data: {
                labels: ['Material de Oficina', 'Servicios', 'Nómina', 'Impuestos', 'Otros'],
                datasets: [{
                    data: [
                        {{ $gastos_oficina ?? 0 }},
                        {{ $gastos_servicios ?? 0 }},
                        {{ $gastos_nomina ?? 0 }},
                        {{ $gastos_impuestos ?? 0 }},
                        {{ $gastos_otros ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Gráfico de Gastos Mensuales
        var mesesCtx = document.getElementById('gastosMensualChart').getContext('2d');
        var mesesChart = new Chart(mesesCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                datasets: [{
                    label: 'Gastos por Mes',
                    data: [
                        {{ $gastos_enero ?? 0 }},
                        {{ $gastos_febrero ?? 0 }},
                        {{ $gastos_marzo ?? 0 }},
                        {{ $gastos_abril ?? 0 }},
                        {{ $gastos_mayo ?? 0 }},
                        {{ $gastos_junio ?? 0 }},
                        {{ $gastos_julio ?? 0 }},
                        {{ $gastos_agosto ?? 0 }},
                        {{ $gastos_septiembre ?? 0 }},
                        {{ $gastos_octubre ?? 0 }},
                        {{ $gastos_noviembre ?? 0 }},
                        {{ $gastos_diciembre ?? 0 }}
                    ],
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection 