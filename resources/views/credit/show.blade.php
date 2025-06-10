@extends('layouts.app')

@section('title', 'Detalles del Crédito')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Crédito #{{ $credit->id }}</h4>
                    <div>
                        <a href="{{ route('credit.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Información del Crédito</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Cliente:</th>
                                            <td>{{ $credit->user->name ?? 'No disponible' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Monto:</th>
                                            <td>${{ number_format($credit->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Interés:</th>
                                            <td>{{ $credit->utility }}%</td>
                                        </tr>
                                        <tr>
                                            <th>Monto Total:</th>
                                            <td>${{ number_format($credit->amount_neto, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Número de Pagos:</th>
                                            <td>{{ $credit->payment_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Monto por Cuota:</th>
                                            <td>${{ number_format($credit->payment_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Frecuencia:</th>
                                            <td>{{ ucfirst($credit->payment_frequency) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                @if($credit->status == 'inprogress')
                                                    <span class="badge bg-primary">En Progreso</span>
                                                @elseif($credit->status == 'completed')
                                                    <span class="badge bg-success">Completado</span>
                                                @elseif($credit->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelado</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($credit->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Solicitud:</th>
                                            <td>{{ $credit->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Estado de Aprobación</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                @if($credit->status == 'pendiente')
                                                    <span class="badge bg-warning text-dark">Pendiente de Aprobación</span>
                                                @elseif($credit->status == 'aprobado')
                                                    <span class="badge bg-success">Aprobado</span>
                                                @elseif($credit->status == 'rechazado')
                                                    <span class="badge bg-danger">Rechazado</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($credit->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($credit->status != 'pendiente')
                                            <tr>
                                                <th>Procesado por:</th>
                                                <td>{{ $credit->approver->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Fecha:</th>
                                                <td>{{ $credit->approval_date ? $credit->approval_date->format('d/m/Y H:i') : 'No disponible' }}</td>
                                            </tr>
                                            @if($credit->approval_notes)
                                                <tr>
                                                    <th>Notas:</th>
                                                    <td>{{ $credit->approval_notes }}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    </table>
                                    
                                    @if($credit->status == 'pendiente' && (Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('admin')))
                                        <div class="mt-3">
                                            <a href="{{ route('credit.approval.form', $credit->id) }}" class="btn btn-primary">
                                                <i class="fas fa-check-circle"></i> Procesar Solicitud
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Agente Asignado</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Nombre:</th>
                                            <td>{{ $credit->agent->name ?? 'No disponible' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $credit->agent->email ?? 'No disponible' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($payments) > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Pagos Realizados</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Monto</th>
                                                <th>Fecha</th>
                                                <th>Método</th>
                                                <th>Notas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->id }}</td>
                                                    <td>${{ number_format($payment->amount, 2) }}</td>
                                                    <td>{{ date('d/m/Y H:i', strtotime($payment->created_at)) }}</td>
                                                    <td>{{ $payment->payment_method ?? 'No disponible' }}</td>
                                                    <td>{{ $payment->notes ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 