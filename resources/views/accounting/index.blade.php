@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contabilidad</h3>
                    <div class="card-tools">
                        <a href="{{ route('accounting.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nueva Entrada
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Formulario de filtros -->
                    <form method="GET" action="{{ route('accounting.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ request('start_date') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Fecha Fin</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ request('end_date') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="type">Tipo</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="">Todos</option>
                                        <option value="ingreso" {{ request('type') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                        <option value="gasto" {{ request('type') == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                        <option value="ajuste" {{ request('type') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Resumen de totales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ingresos</span>
                                    <span class="info-box-number">$ {{ number_format($totals['ingresos'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-minus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Gastos</span>
                                    <span class="info-box-number">$ {{ number_format($totals['gastos'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-sync"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Ajustes</span>
                                    <span class="info-box-number">$ {{ number_format($totals['ajustes'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de entradas contables -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                    <th class="text-right">Monto</th>
                                    <th>Referencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                                        <td>
                                            @switch($entry->entry_type)
                                                @case('ingreso')
                                                    <span class="badge badge-success">Ingreso</span>
                                                    @break
                                                @case('gasto')
                                                    <span class="badge badge-danger">Gasto</span>
                                                    @break
                                                @case('ajuste')
                                                    <span class="badge badge-warning">Ajuste</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $entry->description }}</td>
                                        <td>{{ $entry->category }}</td>
                                        <td class="text-right">$ {{ number_format($entry->amount, 2) }}</td>
                                        <td>{{ $entry->reference }}</td>
                                        <td>
                                            <a href="{{ route('accounting.show', $entry->id) }}" 
                                               class="btn btn-sm btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('accounting.edit', $entry->id) }}" 
                                               class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('accounting.destroy', $entry->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('¿Está seguro de eliminar esta entrada?')"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay entradas contables para mostrar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $entries->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 