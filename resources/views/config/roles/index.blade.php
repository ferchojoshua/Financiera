@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Roles</h4>
                    @if(auth()->user()->can('create', App\Models\Role::class))
                    <a href="{{ route('config.roles.create') }}" class="btn btn-success">
                        <i class="fa fa-plus"></i> Nuevo Rol
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Slug</th>
                                    <th>Descripción</th>
                                    <th>Permisos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->slug }}</td>
                                    <td>{{ $role->description }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $role->id }}">
                                            <i class="fa fa-eye"></i> Ver Permisos
                                        </button>
                                        
                                        <!-- Modal de Permisos -->
                                        <div class="modal fade" id="permissionsModal{{ $role->id }}" tabindex="-1" aria-labelledby="permissionsModalLabel{{ $role->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title" id="permissionsModalLabel{{ $role->id }}">Permisos del Rol: {{ $role->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @if($role->permissions->count() > 0)
                                                            <div class="row">
                                                                @foreach($role->permissions->sortBy('module') as $permission)
                                                                    <div class="col-md-4 mb-2">
                                                                        <div class="card h-100">
                                                                            <div class="card-header bg-light">
                                                                                <strong>{{ ucfirst($permission->module) }}</strong>
                                                                            </div>
                                                                            <div class="card-body">
                                                                                <p class="mb-1"><strong>{{ $permission->name }}</strong></p>
                                                                                <small class="text-muted">{{ $permission->description }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-center">Este rol no tiene permisos asignados.</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(auth()->user()->can('update', $role))
                                            <a href="{{ route('config.roles.edit', $role->id) }}" class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @endif
                                            
                                            @if(auth()->user()->can('delete', $role))
                                            <form action="{{ route('config.roles.destroy', $role->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Está seguro que desea eliminar este rol?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 