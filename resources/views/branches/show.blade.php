@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles de Sucursal: {{ $branch->name }}</h4>
                    <div>
                        <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('branches.index') }}" class="btn btn-light ms-2">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Información general -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Información General</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Código:</th>
                                                <td>{{ $branch->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nombre:</th>
                                                <td>{{ $branch->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dirección:</th>
                                                <td>{{ $branch->address ?? 'No especificada' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Ciudad:</th>
                                                <td>{{ $branch->city ?? 'No especificada' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Estado/Provincia:</th>
                                                <td>{{ $branch->state ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>País:</th>
                                                <td>{{ $branch->country ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Teléfono:</th>
                                                <td>{{ $branch->phone ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td>{{ $branch->email ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Estado:</th>
                                                <td>
                                                    @if($branch->status == 'active')
                                                        <span class="badge bg-success">Activa</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactiva</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Fecha creación:</th>
                                                <td>{{ $branch->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estadísticas y gerente -->
                        <div class="col-md-6">
                            <!-- Tarjeta de gerente -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Gerente Asignado</h5>
                                </div>
                                <div class="card-body">
                                    @if($branch->manager)
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar me-3">
                                                <i class="fa fa-user-circle fa-3x text-primary"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $branch->manager->name }}</h5>
                                                <p class="text-muted mb-0">{{ $branch->manager->email }}</p>
                                                <p class="mb-0">
                                                    <span class="badge bg-primary">{{ ucfirst($branch->manager->role) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fa fa-exclamation-triangle"></i> No hay gerente asignado a esta sucursal.
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Tarjeta de estadísticas -->
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Estadísticas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-0">{{ $stats['users_count'] }}</h3>
                                                    <p class="text-muted">Usuarios</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-0">{{ $stats['wallets_count'] }}</h3>
                                                    <p class="text-muted">Carteras</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-0">{{ $stats['active_credits'] }}</h3>
                                                    <p class="text-muted">Créditos Activos</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="mb-0">{{ $stats['closed_credits'] }}</h3>
                                                    <p class="text-muted">Créditos Cerrados</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card bg-light mt-2">
                                        <div class="card-body text-center">
                                            <h5>Total en Créditos Activos</h5>
                                            <h3 class="text-success">${{ number_format($stats['total_amount'], 2) }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    @if($branch->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">Descripción</h5>
                                </div>
                                <div class="card-body">
                                    {{ $branch->description }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 