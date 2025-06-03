@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edición de Clientes</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Busque y edite la información de los clientes.
            </div>
            
            <form action="{{ url('supervisor/client/search') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, teléfono o documento...">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Documento</th>
                            <th>Dirección</th>
                            <th>Agente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se mostrarán los resultados de la búsqueda -->
                        <tr>
                            <td colspan="6" class="text-center">Realice una búsqueda para ver resultados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 