@extends('layouts.app')

@section('title', 'Seleccionar Ruta para Asignación de Créditos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Seleccionar Ruta para Asignación de Créditos</h3>
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
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cobrador</th>
                                    <th>Supervisor</th>
                                    <th>Zona</th>
                                    <th>Sucursal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($routes as $route)
                                    <tr>
                                        <td>{{ $route->name }}</td>
                                        <td>{{ optional($route->collector)->name }}</td>
                                        <td>{{ optional($route->supervisor)->name }}</td>
                                        <td>{{ $route->zone }}</td>
                                        <td>{{ optional($route->branch)->name }}</td>
                                        <td>
                                            <a href="{{ route('routes.assign_credits', $route->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-tasks"></i> Asignar Créditos
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay rutas activas disponibles</td>
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
@endsection 