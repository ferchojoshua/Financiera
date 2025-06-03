@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Sucursales</h4>
                    <a href="{{ route('branches.create') }}" class="btn btn-light">
                        <i class="fa fa-plus"></i> Nueva Sucursal
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Ciudad</th>
                                    <th>Teléfono</th>
                                    <th>Gerente</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($branches) > 0)
                                    @foreach($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->code }}</td>
                                            <td>{{ $branch->name }}</td>
                                            <td>{{ $branch->city ?? 'N/A' }}</td>
                                            <td>{{ $branch->phone ?? 'N/A' }}</td>
                                            <td>
                                                @if($branch->manager)
                                                    {{ $branch->manager->name }}
                                                @else
                                                    <span class="text-muted">No asignado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($branch->status == 'active')
                                                    <span class="badge bg-success">Activa</span>
                                                @else
                                                    <span class="badge bg-danger">Inactiva</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta sucursal?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No hay sucursales registradas</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 