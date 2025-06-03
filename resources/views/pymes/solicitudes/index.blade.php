@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Solicitudes de Crédito</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h5>Listado de Solicitudes</h5>
                        </div>
                        <div>
                            <a href="{{ route('pymes.solicitudes.create') }}" class="btn btn-success">
                                <i class="fa fa-plus-circle"></i> Nueva Solicitud
                            </a>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="get" class="row g-3">
                                <div class="col-md-3">
                                    <label for="filtro_cliente" class="form-label">Cliente</label>
                                    <input type="text" class="form-control" id="filtro_cliente" name="cliente" placeholder="Nombre o razón social">
                                </div>
                                <div class="col-md-2">
                                    <label for="filtro_estado" class="form-label">Estado</label>
                                    <select class="form-select" id="filtro_estado" name="estado">
                                        <option value="">Todos</option>
                                        <option value="pending">Pendiente</option>
                                        <option value="under_review">En revisión</option>
                                        <option value="approved">Aprobado</option>
                                        <option value="rejected">Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filtro_monto" class="form-label">Monto mínimo</label>
                                    <input type="number" class="form-control" id="filtro_monto" name="monto_min" placeholder="$">
                                </div>
                                <div class="col-md-3">
                                    <label for="filtro_fecha" class="form-label">Fecha solicitud</label>
                                    <input type="date" class="form-control" id="filtro_fecha" name="fecha">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Tipo de Crédito</th>
                                    <th>Monto Solicitado</th>
                                    <th>Plazo (meses)</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Analista</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($solicitudes) > 0)
                                    @foreach($solicitudes as $solicitud)
                                    <tr>
                                        <td>{{ $solicitud->id }}</td>
                                        <td>{{ $solicitud->client->business_name ?? $solicitud->client->name }}</td>
                                        <td>{{ $solicitud->loan_type }}</td>
                                        <td>${{ number_format($solicitud->amount_requested, 2) }}</td>
                                        <td>{{ $solicitud->term_months }}</td>
                                        <td>{{ $solicitud->application_date->format('d/m/Y') }}</td>
                                        <td>{{ $solicitud->analyst->name ?? 'No asignado' }}</td>
                                        <td>
                                            @switch($solicitud->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                    @break
                                                @case('under_review')
                                                    <span class="badge bg-info">En revisión</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Aprobado</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rechazado</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $solicitud->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('pymes.solicitudes.show', $solicitud->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pymes.analisis.create', $solicitud->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-chart-bar"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center">No hay solicitudes de crédito registradas</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $solicitudes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 