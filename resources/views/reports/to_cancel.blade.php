@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">Préstamos Por Cancelar (Próximos a Vencer)</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Este reporte muestra los préstamos que están próximos a vencer (últimos 3 días de plazo)
                    </div>
                    
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Filtros</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('reports.to_cancel') }}" method="GET" class="form-inline">
                                        <div class="form-group mb-2 mr-2">
                                            <label for="user_id" class="mr-2">Agente:</label>
                                            <select name="user_id" id="user_id" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach(\App\Models\User::where('level', 'agent')->orderBy('name')->get() as $user)
                                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
                                        <a href="{{ route('reports.to_cancel') }}" class="btn btn-secondary mb-2 ml-2">Limpiar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de préstamos -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Interés</th>
                                    <th>Fecha Desembolso</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Días Restantes</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits as $credit)
                                    <tr>
                                        <td>{{ $credit->id }}</td>
                                        <td>
                                            @if($credit->client)
                                                {{ $credit->client->name }} {{ $credit->client->last_name }}
                                            @else
                                                Cliente no encontrado
                                            @endif
                                        </td>
                                        <td>{{ number_format($credit->amount, 2) }}</td>
                                        <td>{{ number_format($credit->interest_amount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($credit->disbursement_date)->format('d/m/Y') }}</td>
                                        <td>{{ $credit->due_date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-warning">{{ $credit->remaining_days }} días</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ ucfirst($credit->status) }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('credit.show', $credit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No hay préstamos por vencer en este momento</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $credits->appends(request()->except('page'))->links() }}
                    </div>
                    
                    <!-- Botones de exportación -->
                    <div class="mt-4">
                        <a href="{{ route('reports.export', ['to_cancel', 'pdf']) }}?{{ http_build_query(request()->except('page')) }}" class="btn btn-danger">
                            <i class="fa fa-file-pdf"></i> Exportar a PDF
                        </a>
                        <a href="{{ route('reports.export', ['to_cancel', 'excel']) }}?{{ http_build_query(request()->except('page')) }}" class="btn btn-success ml-2">
                            <i class="fa fa-file-excel"></i> Exportar a Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
</style>
@endsection 