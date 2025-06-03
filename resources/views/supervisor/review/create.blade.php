@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Revisión de Cartera</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Realice una revisión detallada de la cartera de los agentes.
            </div>
            
            <form action="{{ url('supervisor/review/store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="agent">Seleccione Agente</label>
                        <select name="agent_id" id="agent" class="form-select">
                            <option value="">-- Seleccione un agente --</option>
                            <!-- Aquí se mostrarán los agentes -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="date">Fecha de Revisión</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="findings">Hallazgos</label>
                        <textarea name="findings" id="findings" class="form-control" rows="4" placeholder="Detalle los hallazgos encontrados durante la revisión..."></textarea>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="recommendations">Recomendaciones</label>
                        <textarea name="recommendations" id="recommendations" class="form-control" rows="4" placeholder="Detalle las recomendaciones para el agente..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="action_plan">Plan de Acción</label>
                        <textarea name="action_plan" id="action_plan" class="form-control" rows="4" placeholder="Detalle el plan de acción a seguir..."></textarea>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Guardar Revisión
                    </button>
                </div>
            </form>
            
            <hr>
            
            <h5>Revisiones Anteriores</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Agente</th>
                            <th>Hallazgos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se mostrarán las revisiones anteriores -->
                        <tr>
                            <td colspan="5" class="text-center">No hay revisiones registradas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 