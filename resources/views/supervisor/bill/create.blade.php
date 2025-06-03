@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Registro de Gastos</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Registre los gastos operativos del equipo.
            </div>
            
            <form action="{{ url('supervisor/bill') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="agent">Asignar a Agente (Opcional)</label>
                        <select name="agent_id" id="agent" class="form-select">
                            <option value="">-- No asignar a agente --</option>
                            <!-- Aquí se mostrarán los agentes -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="amount">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date">Fecha</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="type">Tipo de Gasto</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">-- Seleccione tipo --</option>
                            <option value="transporte">Transporte</option>
                            <option value="alimentacion">Alimentación</option>
                            <option value="papeleria">Papelería</option>
                            <option value="comunicacion">Comunicación</option>
                            <option value="mantenimiento">Mantenimiento</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="description">Descripción</label>
                        <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="receipt">Comprobante (Opcional)</label>
                        <input type="file" name="receipt" id="receipt" class="form-control">
                        <small class="form-text text-muted">Formatos aceptados: JPG, PNG, PDF. Máximo 2MB.</small>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Registrar Gasto
                    </button>
                </div>
            </form>
            
            <hr>
            
            <h5>Gastos Recientes</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Agente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se mostrarán los gastos recientes -->
                        <tr>
                            <td colspan="6" class="text-center">No hay gastos registrados</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 