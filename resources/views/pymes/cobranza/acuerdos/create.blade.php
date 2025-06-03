@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Nuevo Acuerdo de Pago</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Registre un nuevo acuerdo de pago para la reestructuración de créditos PYMES.
                        </div>
                    
                    <form action="{{ url('pymes/cobranza/acuerdos') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="credito_id">Seleccionar Crédito</label>
                                <select name="credito_id" id="credito_id" class="form-select" required>
                                    <option value="">-- Seleccione un crédito --</option>
                                    <!-- Aquí se cargarán los créditos -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_acuerdo">Tipo de Acuerdo</label>
                                <select name="tipo_acuerdo" id="tipo_acuerdo" class="form-select" required>
                                    <option value="">-- Seleccione tipo de acuerdo --</option>
                                    <option value="refinanciacion">Refinanciación</option>
                                    <option value="reestructuracion">Reestructuración</option>
                                    <option value="condonacion">Condonación parcial</option>
                                    <option value="aplazamiento">Aplazamiento</option>
                                    <option value="extension">Extensión de plazo</option>
                                    <option value="pago_unico">Pago único</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fecha_acuerdo">Fecha de Acuerdo</label>
                                <input type="date" name="fecha_acuerdo" id="fecha_acuerdo" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="monto_total">Monto Total</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto_total" id="monto_total" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="plazo_meses">Plazo en Meses</label>
                                <input type="number" name="plazo_meses" id="plazo_meses" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monto_cuota">Monto Cuota</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto_cuota" id="monto_cuota" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tasa_interes">Tasa de Interés (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="tasa_interes" id="tasa_interes" class="form-control" placeholder="0.00" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_primer_pago">Fecha Primer Pago</label>
                                <input type="date" name="fecha_primer_pago" id="fecha_primer_pago" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dia_pago">Día de Pago Mensual</label>
                                <input type="number" min="1" max="31" name="dia_pago" id="dia_pago" class="form-control" placeholder="Día del mes" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="condiciones">Condiciones del Acuerdo</label>
                                <textarea name="condiciones" id="condiciones" class="form-control" rows="4" required placeholder="Detalle las condiciones del acuerdo..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firmado_por_cliente">Firmado por Cliente</label>
                                <select name="firmado_por_cliente" id="firmado_por_cliente" class="form-select" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                    </select>
                            </div>
                            <div class="col-md-6">
                                <label for="firmado_por_empresa">Firmado por Empresa</label>
                                <select name="firmado_por_empresa" id="firmado_por_empresa" class="form-select" required>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                    </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="estado">Estado del Acuerdo</label>
                                <select name="estado" id="estado" class="form-select" required>
                                    <option value="borrador">Borrador</option>
                                    <option value="pendiente">Pendiente de aprobación</option>
                                    <option value="aprobado">Aprobado</option>
                                    <option value="activo">Activo</option>
                                    <option value="incumplido">Incumplido</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                                </div>
                            <div class="col-md-6">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url('pymes/cobranza') }}" class="btn btn-secondary me-md-2">
                                <i class="fa fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Acuerdo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 