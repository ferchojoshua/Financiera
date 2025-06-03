@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Gestión de Clientes PYME</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h5>Listado de Empresas</h5>
                        </div>
                        <div>
                            <a href="{{ route('pymes.clientes.create') }}" class="btn btn-success">
                                <i class="fa fa-plus-circle"></i> Nueva Empresa
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Razón Social</th>
                                    <th>Sector</th>
                                    <th>Actividad Económica</th>
                                    <th>NIT/RUC</th>
                                    <th>Ingresos Anuales</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($clientes) > 0)
                                    @foreach($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->business_name }}</td>
                                        <td>{{ $cliente->business_sector }}</td>
                                        <td>{{ $cliente->economic_activity }}</td>
                                        <td>{{ $cliente->tax_id }}</td>
                                        <td>${{ number_format($cliente->annual_revenue, 2) }}</td>
                                        <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($cliente->status == 'good')
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pymes.clientes.show', $cliente->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-warning">
                                                <i class="fa fa-file-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No hay clientes PYME registrados</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $clientes->links() }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Estadísticas de Clientes PYME</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Empresas</h6>
                                    <h3 class="card-text">{{ count($clientes) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Empresas Activas</h6>
                                    <h3 class="card-text">{{ $clientes->where('status', 'good')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Créditos Activos</h6>
                                    <h3 class="card-text">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">En Mora</h6>
                                    <h3 class="card-text">0</h3>
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