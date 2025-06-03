@extends('layouts.app')

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Registro de Auditoría</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="icon fa fa-info-circle"></i>
                                En esta sección podrás consultar las acciones realizadas en el sistema.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="row">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('pymes.auditoria') }}" class="form-inline mb-3">
                                <div class="form-group mr-2">
                                    <label for="date_from" class="mr-1">Desde:</label>
                                    <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_to" class="mr-1">Hasta:</label>
                                    <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="user_id" class="mr-1">Usuario:</label>
                                    <select id="user_id" name="user_id" class="form-control">
                                        <option value="">Todos</option>
                                        <!-- Aquí se cargarían los usuarios desde la base de datos -->
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="action" class="mr-1">Acción:</label>
                                    <select id="action" name="action" class="form-control">
                                        <option value="">Todas</option>
                                        <option value="create">Creación</option>
                                        <option value="update">Actualización</option>
                                        <option value="delete">Eliminación</option>
                                        <option value="login">Inicio de sesión</option>
                                        <option value="logout">Cierre de sesión</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tabla de registros -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se cargarían los registros de auditoría desde la base de datos -->
                                <tr>
                                    <td colspan="6" class="text-center">No hay registros de auditoría disponibles</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <!-- Aquí iría la paginación -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Código JavaScript para la funcionalidad de la página
    });
</script>
@endsection 