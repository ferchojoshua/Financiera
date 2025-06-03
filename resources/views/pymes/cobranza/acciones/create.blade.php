@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Nueva Acción de Cobranza</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Registre una nueva acción de cobranza para seguimiento de créditos PYMES.
                        </div>
                    
                    <form action="{{ url('pymes/cobranza/acciones') }}" method="POST">
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
                                <label for="tipo_accion">Tipo de Acción</label>
                                <select name="tipo_accion" id="tipo_accion" class="form-select" required>
                                    <option value="">-- Seleccione tipo de acción --</option>
                                    <option value="llamada">Llamada telefónica</option>
                                    <option value="correo">Correo electrónico</option>
                                    <option value="visita">Visita personal</option>
                                    <option value="mensaje">Mensaje SMS/WhatsApp</option>
                                    <option value="carta">Carta formal</option>
                                    <option value="notificacion">Notificación legal</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_accion">Fecha de Acción</label>
                                <input type="date" name="fecha_accion" id="fecha_accion" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="responsable">Responsable</label>
                                <input type="text" name="responsable" id="responsable" class="form-control" value="{{ Auth::user()->name }}" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="detalle_accion">Detalle de la Acción</label>
                                <textarea name="detalle_accion" id="detalle_accion" class="form-control" rows="3" required placeholder="Describa la acción realizada..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="resultado">Resultado</label>
                                <select name="resultado" id="resultado" class="form-select" required>
                                    <option value="">-- Seleccione resultado --</option>
                                    <option value="exitoso">Exitoso</option>
                                    <option value="parcial">Parcialmente exitoso</option>
                                    <option value="pendiente">Pendiente de respuesta</option>
                                    <option value="fallido">Sin éxito</option>
                                    <option value="imposible">Imposible contactar</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="monto_compromiso">Monto Compromiso (Opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="monto_compromiso" id="monto_compromiso" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_compromiso">Fecha de Compromiso (Opcional)</label>
                                <input type="date" name="fecha_compromiso" id="fecha_compromiso" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="proxima_accion">Próxima Acción</label>
                                <select name="proxima_accion" id="proxima_accion" class="form-select" required>
                                    <option value="">-- Seleccione próxima acción --</option>
                                    <option value="seguimiento">Seguimiento telefónico</option>
                                    <option value="visita">Visita de seguimiento</option>
                                    <option value="acuerdo">Formalizar acuerdo</option>
                                    <option value="notificacion">Enviar notificación</option>
                                    <option value="legal">Proceder legalmente</option>
                                    <option value="ninguna">Ninguna acción requerida</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_proxima_accion">Fecha Próxima Acción</label>
                                <input type="date" name="fecha_proxima_accion" id="fecha_proxima_accion" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="observaciones">Observaciones Adicionales</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url('pymes/cobranza') }}" class="btn btn-secondary me-md-2">
                                <i class="fa fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Acción
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 