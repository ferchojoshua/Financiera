@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Configuración del Sistema</h4>
                </div>
                <div class="card-body">
                    <style>
                        .config-card {
                            height: 350px;
                            display: flex;
                            flex-direction: column;
                            transition: transform 0.3s, box-shadow 0.3s;
                        }
                        .config-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                        }
                        .config-card .card-body {
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            flex-grow: 1;
                            padding: 1.5rem;
                        }
                        .config-card .icon-container {
                            height: 100px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 1rem;
                        }
                        .config-card .btn-container {
                            margin-top: auto;
                            padding-top: 1rem;
                        }
                        .config-card .card-title {
                            font-size: 1.25rem;
                            font-weight: 600;
                            margin-bottom: 0.75rem;
                        }
                        .config-card .card-text {
                            flex-grow: 1;
                            font-size: 0.9rem;
                        }
                    </style>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fa fa-building fa-4x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Datos de la Empresa</h5>
                                    <p class="card-text">Configura la información de tu empresa como nombre, RUC, dirección, logo y más.</p>
                                    <div class="btn-container">
                                        <a href="{{ route('config.company.edit') }}" class="btn btn-primary w-100">
                                            <i class="fa fa-edit"></i> Editar Información
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fa fa-users fa-4x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Gestión de Usuarios</h5>
                                    <p class="card-text">Administra los usuarios del sistema, asigna roles y permisos.</p>
                                    <div class="btn-container">
                                        <a href="{{ route('config.users.index') }}" class="btn btn-success w-100">
                                            <i class="fa fa-list"></i> Ver Usuarios
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fa fa-user-plus fa-4x text-info"></i>
                                    </div>
                                    <h5 class="card-title">Crear Usuario</h5>
                                    <p class="card-text">Crea nuevos usuarios para acceder al sistema con diferentes roles.</p>
                                    <div class="btn-container">
                                        <a href="{{ route('config.users.create') }}" class="btn btn-info w-100">
                                            <i class="fa fa-plus"></i> Nuevo Usuario
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fa fa-money fa-4x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">Billeteras y Finanzas</h5>
                                    <p class="card-text">Configura las billeteras, cuentas y parámetros financieros del sistema.</p>
                                    <div class="btn-container">
                                        <a href="{{ url('admin/wallet/create') }}" class="btn btn-warning w-100">
                                            <i class="fa fa-plus"></i> Crear Billetera
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fas fa-cogs fa-4x text-secondary"></i>
                                    </div>
                                    <h5 class="card-title">Preferencias del Sistema</h5>
                                    <p class="card-text">Configure parámetros generales, notificaciones y comportamiento del sistema.</p>
                                    <div class="btn-container">
                                        <a href="{{ route('config.preferences.edit') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-cog"></i> Configurar Preferencias
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fa fa-database fa-4x text-danger"></i>
                                    </div>
                                    <h5 class="card-title">Respaldo y Mantenimiento</h5>
                                    <p class="card-text">Administra respaldos de datos, actualizaciones y mantenimiento del sistema.</p>
                                    <div class="btn-container">
                                        <a href="#" class="btn btn-danger w-100">
                                            <i class="fa fa-database"></i> Gestionar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card config-card">
                                <div class="card-body text-center">
                                    <div class="icon-container">
                                        <i class="fas fa-user-lock fa-4x text-secondary"></i>
                                    </div>
                                    <h5 class="card-title">Permisos de Acceso</h5>
                                    <p class="card-text">Configure los permisos de acceso a los diferentes módulos del sistema para cada rol de usuario.</p>
                                    <div class="btn-container">
                                        <a href="{{ route('config.permisos.index') }}" class="btn btn-secondary w-100">
                                            <i class="fas fa-cog"></i> Configurar Permisos
                                        </a>
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