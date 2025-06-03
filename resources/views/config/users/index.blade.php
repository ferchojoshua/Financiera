@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold"><i class="fas fa-users mr-2"></i>Gestión de Usuarios</h4>
                    <div>
                        <a href="{{ route('config.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success border-left-4" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger border-left-4" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtros -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-filter mr-2"></i>Filtros de búsqueda</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('config.users.index') }}" class="row g-3">
                                <div class="col-md-3 mb-3">
                                    <label for="search" class="form-label font-weight-bold">Buscar</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nombre, Email, etc.">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="role" class="form-label font-weight-bold">Rol</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Todos los roles</option>
                                        <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Administrador</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                        <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="caja" {{ request('role') == 'caja' ? 'selected' : '' }}>Caja</option>
                                        <option value="colector" {{ request('role') == 'colector' ? 'selected' : '' }}>Colector</option>
                                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Cliente</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label font-weight-bold">Estado</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Todos los estados</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-filter"></i> Filtrar
                                    </button>
                                    <a href="{{ route('config.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-broom"></i> Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover border">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Teléfono</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($users) && $users->count() > 0)
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td><strong>{{ $user->name }} {{ $user->last_name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>
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
                                        </td>
                                        <td>
                                            @if($user->status == 'active')
                                                <span class="badge badge-success text-white">Activo</span>
                                            @else
                                                <span class="badge badge-danger text-white">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->phone ?: 'No disponible' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('config.users.edit', $user->id) }}" class="btn btn-sm btn-warning text-white" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->role != 'superadmin' || auth()->user()->role == 'superadmin')
                                                <form action="{{ route('config.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este usuario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger text-white" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                                            <p>No hay usuarios registrados o que coincidan con los filtros aplicados</p>
                                            @if(request('search') || request('role') || request('status'))
                                                <a href="{{ route('config.users.index') }}" class="btn btn-sm btn-primary mt-2">
                                                    <i class="fas fa-sync"></i> Mostrar todos los usuarios
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        @if(isset($users))
                            {{ $users->appends(request()->except('page'))->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 