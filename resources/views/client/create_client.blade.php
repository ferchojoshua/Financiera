@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Registro Rápido de Cliente</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('client.store') }}" method="POST">
                        @csrf
                        
                        <!-- Información Personal Básica -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre y Apellidos *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nit">Cédula/NIT *</label>
                                    <input type="text" class="form-control" name="nit" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone">Teléfono *</label>
                                    <input type="text" class="form-control" name="phone" required>
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Dirección *</label>
                                    <textarea class="form-control" name="address" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Negocio -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_type">Tipo de Negocio *</label>
                                    <input type="text" class="form-control" name="business_type" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_time">Tiempo del Negocio *</label>
                                    <input type="text" class="form-control" name="business_time" required>
                                </div>
                            </div>
                        </div>

                        <!-- Información Financiera -->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_good">Ventas Días Buenos *</label>
                                    <input type="number" step="0.01" class="form-control" name="sales_good" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_bad">Ventas Días Malos *</label>
                                    <input type="number" step="0.01" class="form-control" name="sales_bad" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weekly_average">Promedio Semanal *</label>
                                    <input type="number" step="0.01" class="form-control" name="weekly_average" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="net_profit">Ganancias Netas *</label>
                                    <input type="number" step="0.01" class="form-control" name="net_profit" required>
                                </div>
                            </div>
                        </div>

                        <!-- Solicitud de Préstamo -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="loan_amount">Monto Solicitado *</label>
                                    <input type="number" step="0.01" class="form-control" name="loan_amount" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_frequency">Frecuencia de Pago *</label>
                                    <select class="form-control" name="payment_frequency" required>
                                        <option value="daily">Diario</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="biweekly">Quincenal</option>
                                        <option value="monthly">Mensual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_payment_date">Fecha Primer Pago *</label>
                                    <input type="date" class="form-control" name="first_payment_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar y Solicitar
                            </button>
                            <a href="{{ route('client.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 