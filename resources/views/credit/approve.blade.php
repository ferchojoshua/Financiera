@extends('layouts.app')

@section('title', 'Aprobar Crédito')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-clipboard-check"></i> Revisión de Crédito</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('credit.index') }}">Créditos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('credit.pending_approval') }}">Pendientes</a></li>
                <li class="breadcrumb-item active">Aprobar Crédito #{{ $credit->id }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Crédito #{{ $credit->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente:</label>
                                <p class="form-control-static">{{ $credit->user->name }}</p>
                            </div>
                            <div class="form-group">
                                <label>Monto Solicitado:</label>
                                <p class="form-control-static">${{ number_format($credit->amount, 2) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Interés:</label>
                                <p class="form-control-static">{{ $credit->utility }}%</p>
                            </div>
                            <div class="form-group">
                                <label>Monto Total a Pagar:</label>
                                <p class="form-control-static">${{ number_format($credit->amount_neto, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Agente:</label>
                                <p class="form-control-static">{{ $credit->agent->name }}</p>
                            </div>
                            <div class="form-group">
                                <label>Número de Pagos:</label>
                                <p class="form-control-static">{{ $credit->payment_number }}</p>
                            </div>
                            <div class="form-group">
                                <label>Monto por Cuota:</label>
                                <p class="form-control-static">${{ number_format($credit->payment_amount, 2) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Frecuencia de Pago:</label>
                                <p class="form-control-static">{{ ucfirst($credit->payment_frequency) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Fecha de Solicitud:</label>
                        <p class="form-control-static">{{ $credit->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Decisión</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('credit.approval.process', $credit->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="notes">Notas:</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Ingrese notas o comentarios sobre su decisión..."></textarea>
                        </div>
                        
                        <div class="form-group text-center">
                            <div class="btn-group btn-group-lg">
                                <button type="submit" name="decision" value="aprobado" class="btn btn-success">
                                    <i class="fas fa-check"></i> Aprobar
                                </button>
                                <button type="submit" name="decision" value="rechazado" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('credit.pending_approval') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 