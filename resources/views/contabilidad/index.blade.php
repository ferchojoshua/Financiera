@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Contabilidad</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Ingresos</h5>
                                    <h2>${{ number_format($ingresos->total ?? 0, 2) }}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('contabilidad.ingresos') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Gastos</h5>
                                    <h2>${{ number_format($gastos->total ?? 0, 2) }}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('contabilidad.gastos') }}">Ver Detalles</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h5>Créditos Activos</h5>
                                    <h2>{{ $creditos->total ?? 0 }}</h2>
                                    <p>Monto total: ${{ number_format($creditos->monto_total ?? 0, 2) }}</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('credit.index') }}">Ver Créditos</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">
                                    <h5>Total Pagos</h5>
                                    <h2>{{ $pagos->total ?? 0 }}</h2>
                                    <p>Monto total: ${{ number_format($pagos->monto_total ?? 0, 2) }}</p>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="{{ route('payment.index') }}">Ver Pagos</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Balance General</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Total Ingresos</td>
                                                    <td>${{ number_format($ingresos->total ?? 0, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Total Gastos</td>
                                                    <td>${{ number_format($gastos->total ?? 0, 2) }}</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Balance</td>
                                                    <td>${{ number_format(($ingresos->total ?? 0) - ($gastos->total ?? 0), 2) }}</td>
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