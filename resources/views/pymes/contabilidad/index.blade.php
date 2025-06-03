@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Contabilidad</h4>
                    <div>
                        <a href="{{ route('pymes.contabilidad.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Entrada
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtros -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('pymes.contabilidad') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Fecha desde</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Fecha hasta</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="entry_type" class="form-label">Tipo</label>
                                <select class="form-control" id="entry_type" name="entry_type">
                                    <option value="">Todos</option>
                                    <option value="ingreso" {{ request('entry_type') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                    <option value="gasto" {{ request('entry_type') == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                    <option value="ajuste" {{ request('entry_type') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Categoría</label>
                                <select class="form-control" id="category" name="category">
                                    <option value="">Todas</option>
                                    <option value="intereses" {{ request('category') == 'intereses' ? 'selected' : '' }}>Intereses</option>
                                    <option value="comisiones" {{ request('category') == 'comisiones' ? 'selected' : '' }}>Comisiones</option>
                                    <option value="personal" {{ request('category') == 'personal' ? 'selected' : '' }}>Personal</option>
                                    <option value="operativo" {{ request('category') == 'operativo' ? 'selected' : '' }}>Operativo</option>
                                    <option value="impuestos" {{ request('category') == 'impuestos' ? 'selected' : '' }}>Impuestos</option>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                                <a href="{{ route('pymes.contabilidad') }}" class="btn btn-secondary">
                                    <i class="fas fa-broom"></i> Limpiar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Resumen -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ingresos</h5>
                                    <h3 class="card-text">€ {{ number_format($totalIngresos ?? 0, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Gastos</h5>
                                    <h3 class="card-text">€ {{ number_format($totalGastos ?? 0, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Balance</h5>
                                    <h3 class="card-text">€ {{ number_format(($totalIngresos ?? 0) - ($totalGastos ?? 0), 2, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Categoría</th>
                                    <th>Monto</th>
                                    <th>Referencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($entries) > 0)
                                    @foreach($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->id }}</td>
                                        <td>{{ date('d/m/Y', strtotime($entry->entry_date)) }}</td>
                                        <td>{{ Str::limit($entry->description, 50) }}</td>
                                        <td>
                                            @if($entry->entry_type == 'ingreso')
                                                <span class="badge bg-success">Ingreso</span>
                                            @elseif($entry->entry_type == 'gasto')
                                                <span class="badge bg-danger">Gasto</span>
                                            @else
                                                <span class="badge bg-warning">Ajuste</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($entry->category) }}</td>
                                        <td class="{{ $entry->entry_type == 'ingreso' ? 'text-success' : ($entry->entry_type == 'gasto' ? 'text-danger' : 'text-warning') }}">
                                            € {{ number_format($entry->amount, 2, ',', '.') }}
                                        </td>
                                        <td>{{ $entry->reference }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No hay entradas contables registradas</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 