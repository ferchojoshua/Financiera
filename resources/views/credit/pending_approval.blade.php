@extends('layouts.app')

@section('title', 'Créditos Pendientes de Aprobación')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-clock"></i> Créditos Pendientes de Aprobación</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('credit.index') }}">Créditos</a></li>
                <li class="breadcrumb-item active">Pendientes de Aprobación</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Solicitudes Pendientes</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-ban"></i> ¡Error!</h5>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(count($credits) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Agente</th>
                                        <th>Monto</th>
                                        <th>Interés</th>
                                        <th>Cuotas</th>
                                        <th>Fecha de Solicitud</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($credits as $credit)
                                        <tr>
                                            <td>{{ $credit->id }}</td>
                                            <td>{{ $credit->user->name }}</td>
                                            <td>{{ $credit->agent->name }}</td>
                                            <td>${{ number_format($credit->amount, 2) }}</td>
                                            <td>{{ $credit->utility }}%</td>
                                            <td>{{ $credit->payment_number }}</td>
                                            <td>{{ $credit->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('credit.approval.form', $credit->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-clipboard-check"></i> Revisar
                                                </a>
                                                <a href="{{ route('credit.show', $credit->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $credits->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i> No hay créditos pendientes de aprobación.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 