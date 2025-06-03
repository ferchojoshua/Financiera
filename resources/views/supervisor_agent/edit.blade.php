@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Modificar Base del Agente</h4>
                    <a href="{{ route('supervisor.agent') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Información del Agente</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nombre:</strong> {{ $name }} {{ $last_name }}</p>
                                            <p><strong>País:</strong> {{ $country }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Cartera:</strong> {{ $wallet_name }}</p>
                                            <p><strong>Ciudad:</strong> {{ $address }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Base Actual:</strong> <span class="text-success">{{ number_format($base_current, 2) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('supervisor.agent.update', $id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="base_number">Monto a Agregar:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="base_number" name="base_number" required>
                            </div>
                            <small class="form-text text-muted">
                                Este monto se agregará a la base actual del agente.
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notas (opcional):</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
