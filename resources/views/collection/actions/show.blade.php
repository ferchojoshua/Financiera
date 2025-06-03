@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalle de Acción de Cobranza</h5>
                        <a href="{{ route('collection.actions.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p>Esta es una vista de implementación básica para mostrar los detalles de una acción de cobranza. Esta vista se completará con la información real en futuras actualizaciones.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Información de la Acción</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>ID:</strong> #{{ $id }}</p>
                                    <p><strong>Tipo:</strong> Registro de Gestión</p>
                                    <p><strong>Fecha:</strong> {{ date('d/m/Y') }}</p>
                                    <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Acciones Disponibles</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('collection.actions.edit', $id) }}" class="btn btn-warning">
                                            <i class="fa fa-edit"></i> Editar Acción
                                        </a>
                                        <a href="#" class="btn btn-info">
                                            <i class="fa fa-print"></i> Imprimir Reporte
                                        </a>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                            <i class="fa fa-trash"></i> Eliminar Acción
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Información del Crédito Asociado</h6>
                                </div>
                                <div class="card-body">
                                    <p>Información detallada del crédito asociado a esta acción de cobranza.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro que desea eliminar esta acción de cobranza? Esta operación no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="{{ route('collection.actions.destroy', $id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 