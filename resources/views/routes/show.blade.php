@extends('layouts.app')
@section('title', 'Detalle de Ruta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Información de la Ruta: {{ $route->name }}</h4>
                <div class="card-tools">
                    <a href="{{ route('routes.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('routes.edit', $route->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="{{ route('routes.assign_credits', $route->id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-link"></i> Asignar Préstamos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <p>{{ $route->name }}</p>
                        </div>
                        <div class="form-group">
                            <label>Descripción:</label>
                            <p>{{ $route->description ?: 'No especificada' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Zona:</label>
                            <p>{{ $route->zone ?: 'No especificada' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Días de visita:</label>
                            <p>
                                @php
                                    $days = json_decode($route->days);
                                    $dayNames = [
                                        'monday' => 'Lunes',
                                        'tuesday' => 'Martes',
                                        'wednesday' => 'Miércoles',
                                        'thursday' => 'Jueves',
                                        'friday' => 'Viernes',
                                        'saturday' => 'Sábado',
                                        'sunday' => 'Domingo'
                                    ];
                                    $displayDays = [];
                                    foreach ($days as $day) {
                                        $displayDays[] = $dayNames[$day] ?? $day;
                                    }
                                    echo implode(', ', $displayDays);
                                @endphp
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cobrador:</label>
                            <p>{{ $route->collector ? $route->collector->name . ' ' . ($route->collector->last_name ?? '') : 'No asignado' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Supervisor:</label>
                            <p>{{ $route->supervisor ? $route->supervisor->name . ' ' . ($route->supervisor->last_name ?? '') : 'No asignado' }}</p>
                        </div>
                        <div class="form-group">
                            <label>Estado:</label>
                            <p>
                                @if($route->status == 'active')
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-danger">Inactiva</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Estadísticas de la Ruta</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Clientes</span>
                                <span class="info-box-number">{{ $stats['total_clients'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-file-invoice-dollar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Préstamos</span>
                                <span class="info-box-number">{{ $stats['total_credits'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Monto Total</span>
                                <span class="info-box-number">{{ number_format($stats['total_amount'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Préstamos Vencidos</span>
                                <span class="info-box-number">{{ $stats['overdue_credits'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Clientes en esta Ruta</h4>
            </div>
            <div class="card-body">
                @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->name }} {{ $client->last_name ?? '' }}</td>
                                <td>{{ $client->phone ?? 'No disponible' }}</td>
                                <td>{{ $client->address ?? 'No disponible' }}</td>
                                <td>
                                    <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    No hay clientes asignados a esta ruta.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 