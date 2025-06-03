@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Panel de Supervisor</h4>
                </div>
                <div class="card-body">
                    <!-- Resumen de estadísticas -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Agentes</h5>
                                    <h2>{{ $totalAgents }}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('supervisor.agent') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Rutas</h5>
                                    <h2>{{ $totalRoutes }}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('route.index') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Créditos</h5>
                                    <h2>{{ $totalCredits }}</h2>
                                    <p>Monto total: ${{ number_format($totalCreditAmount, 2) }}</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('credit.index') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Pagos</h5>
                                    <h2>{{ $totalPayments }}</h2>
                                    <p>Monto total: ${{ number_format($totalPaymentAmount, 2) }}</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('payment.index') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acceso rápido a funciones -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Acciones Rápidas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supervisor.agent') }}" class="btn btn-lg btn-primary btn-block">
                                                <i class="fas fa-user-tie fa-2x mb-2"></i><br>
                                                Gestionar Agentes
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supervisor.tracker') }}" class="btn btn-lg btn-success btn-block">
                                                <i class="fas fa-search-location fa-2x mb-2"></i><br>
                                                Rastreo
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supervisor.cash') }}" class="btn btn-lg btn-info btn-block">
                                                <i class="fas fa-cash-register fa-2x mb-2"></i><br>
                                                Caja
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="{{ route('supervisor.statistics') }}" class="btn btn-lg btn-warning btn-block">
                                                <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                                                Estadísticas
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de agentes -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Agentes Activos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
                                                    <th>Rutas Asignadas</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($agents as $agent)
                                                <tr>
                                                    <td>{{ $agent->id }}</td>
                                                    <td>{{ $agent->name }}</td>
                                                    <td>{{ $agent->email }}</td>
                                                    <td>{{ $agent->phone ?? 'N/A' }}</td>
                                                    <td>{{ $agent->routes->count() ?? 0 }}</td>
                                                    <td>
                                                        <a href="{{ route('supervisor.agent.edit', $agent->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No hay agentes registrados</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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
@endsection 