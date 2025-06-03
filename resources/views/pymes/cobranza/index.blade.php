@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Gestión de Cobranza PYMES</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Administre las actividades de cobranza y seguimiento de créditos a PYMES.
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form action="{{ url('pymes/cobranza/search') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <select name="filter" class="form-select">
                                        <option value="all">Todos los créditos</option>
                                        <option value="overdue">Vencidos</option>
                                        <option value="risk">En riesgo</option>
                                        <option value="current">Al día</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, ID o empresa...">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ url('pymes/cobranza/acciones/create') }}" class="btn btn-success me-2">
                                <i class="fa fa-plus-circle"></i> Nueva Acción
                            </a>
                            <a href="{{ url('pymes/cobranza/acuerdos/create') }}" class="btn btn-info">
                                <i class="fa fa-handshake-o"></i> Nuevo Acuerdo
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="display-4 mb-0">0</h3>
                                    <p class="mb-0">Créditos Activos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="display-4 mb-0">0</h3>
                                    <p class="mb-0">Al Día</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="display-4 mb-0">0</h3>
                                    <p class="mb-0">En Riesgo</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="display-4 mb-0">0</h3>
                                    <p class="mb-0">Vencidos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Empresa</th>
                                    <th>Contacto</th>
                                    <th>Monto</th>
                                    <th>Último Pago</th>
                                    <th>Próximo Pago</th>
                                    <th>Estado</th>
                                    <th>Días Mora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se mostrarán los créditos -->
                                <tr>
                                    <td colspan="9" class="text-center">No hay créditos registrados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <h5>Acciones de Cobranza Recientes</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Resultado</th>
                                    <th>Responsable</th>
                                    <th>Próxima Acción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se mostrarán las acciones recientes -->
                                <tr>
                                    <td colspan="7" class="text-center">No hay acciones registradas</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 