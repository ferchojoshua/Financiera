@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Balance General</h4>
                    <a href="{{ route('contabilidad.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Ingresos</h5>
                                    <h2 class="card-text">${{ number_format($ingresos->total ?? 0, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Gastos</h5>
                                    <h2 class="card-text">${{ number_format($gastos->total ?? 0, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ ($balance >= 0) ? 'bg-success' : 'bg-danger' }} text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Balance Neto</h5>
                                    <h2 class="card-text">${{ number_format($balance, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Detalle del Balance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th class="text-right">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Total Ingresos</td>
                                                    <td class="text-right text-success">${{ number_format($ingresos->total ?? 0, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Total Gastos</td>
                                                    <td class="text-right text-danger">${{ number_format($gastos->total ?? 0, 2) }}</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Balance</td>
                                                    <td class="text-right {{ ($balance >= 0) ? 'text-success' : 'text-danger' }}">${{ number_format($balance, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 