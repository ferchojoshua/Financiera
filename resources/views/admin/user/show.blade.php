@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 font-weight-bold"><i class="fas fa-user mr-2"></i>Detalles del Usuario</h4>
                        <div>
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-outline-light btn-sm ml-2">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div class="avatar-circle mb-3">
                                                <span class="initials">{{ substr($user->name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                                            </div>
                                            <h4 class="font-weight-bold">{{ $user->name }} {{ $user->last_name }}</h4>
                                            <p class="mb-1">
                                                @if($user->role == 'superadmin')
                                                    <span class="badge badge-danger text-white">Super Admin</span>
                                                @elseif($user->role == 'admin')
                                                    <span class="badge badge-primary text-white">Administrador</span>
                                                @elseif($user->role == 'supervisor')
                                                    <span class="badge badge-success text-white">Supervisor</span>
                                                @elseif($user->role == 'caja')
                                                    <span class="badge badge-info text-white">Caja</span>
                                                @elseif($user->role == 'colector')
                                                    <span class="badge badge-warning text-dark">Colector</span>
                                                @else
                                                    <span class="badge badge-secondary text-white">Cliente</span>
                                                @endif
                                            </p>
                                            <p class="mb-0">
                                                @if($user->status == 'active')
                                                    <span class="badge badge-success text-white">Activo</span>
                                                @else
                                                    <span class="badge badge-danger text-white">Inactivo</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-9">
                                            <h5 class="border-bottom pb-2 mb-3">Información de Contacto</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">ID de Usuario</p>
                                                    <p class="font-weight-bold">{{ $user->id }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Fecha de Registro</p>
                                                    <p class="font-weight-bold">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Correo Electrónico</p>
                                                    <p class="font-weight-bold">{{ $user->email }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Teléfono</p>
                                                    <p class="font-weight-bold">{{ $user->phone ?: 'No disponible' }}</p>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <p class="mb-1 text-muted">Dirección</p>
                                                    <p class="font-weight-bold">{{ $user->address ?: 'No disponible' }}</p>
                                                </div>
                                            </div>
                                            
                                            <h5 class="border-bottom pb-2 mb-3 mt-4">Detalles del Sistema</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Rol</p>
                                                    <p class="font-weight-bold">{{ ucfirst($user->role) }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Estado</p>
                                                    <p class="font-weight-bold">{{ $user->status == 'active' ? 'Activo' : 'Inactivo' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <p class="mb-1 text-muted">Última Actualización</p>
                                                    <p class="font-weight-bold">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
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
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #0275d8;
        text-align: center;
        border-radius: 50%;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .initials {
        position: relative;
        font-size: 32px;
        line-height: 100px;
        color: #fff;
        font-weight: bold;
    }
</style>
@endpush 