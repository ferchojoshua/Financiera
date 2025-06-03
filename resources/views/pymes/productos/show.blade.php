@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Producto Financiero</h4>
                    <div>
                        <a href="{{ route('pymes.productos') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3>{{ $producto->nombre }}</h3>
                            <span class="badge {{ $producto->activo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">ID:</div>
                        <div class="col-md-9">{{ $producto->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Descripción:</div>
                        <div class="col-md-9">
                            {{ $producto->descripcion ?? 'Sin descripción' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Tasa de Interés:</div>
                        <div class="col-md-9">{{ $producto->tasa_interes }}%</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Plazo Máximo:</div>
                        <div class="col-md-9">{{ $producto->plazo_maximo }} meses</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Monto Mínimo:</div>
                        <div class="col-md-9">€ {{ number_format($producto->monto_minimo, 2, ',', '.') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Monto Máximo:</div>
                        <div class="col-md-9">€ {{ number_format($producto->monto_maximo, 2, ',', '.') }}</div>
                    </div>

                    @if(isset($producto->comision_apertura))
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Comisión de Apertura:</div>
                        <div class="col-md-9">{{ $producto->comision_apertura }}%</div>
                    </div>
                    @endif

                    @if(isset($producto->requisitos))
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Requisitos:</div>
                        <div class="col-md-9">
                            {!! nl2br(e($producto->requisitos)) !!}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Creado:</div>
                        <div class="col-md-9">{{ $producto->created_at ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Última actualización:</div>
                        <div class="col-md-9">{{ $producto->updated_at ?? 'N/A' }}</div>
                    </div>

                    <hr>

                    <h5 class="mt-4 mb-3">Simulación de pagos</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Ejemplo con monto mínimo</h6>
                                    <p class="card-text">
                                        <strong>Préstamo:</strong> € {{ number_format($producto->monto_minimo, 2, ',', '.') }}<br>
                                        <strong>Plazo:</strong> 12 meses<br>
                                        <strong>Tasa:</strong> {{ $producto->tasa_interes }}%<br>
                                        <strong>Cuota mensual:</strong> € {{ number_format(($producto->monto_minimo * (($producto->tasa_interes/100)/12) * pow(1 + (($producto->tasa_interes/100)/12), 12)) / (pow(1 + (($producto->tasa_interes/100)/12), 12) - 1), 2, ',', '.') }}<br>
                                        <strong>Total a pagar:</strong> € {{ number_format((($producto->monto_minimo * (($producto->tasa_interes/100)/12) * pow(1 + (($producto->tasa_interes/100)/12), 12)) / (pow(1 + (($producto->tasa_interes/100)/12), 12) - 1)) * 12, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Ejemplo con monto medio</h6>
                                    @php
                                        $montoMedio = ($producto->monto_minimo + $producto->monto_maximo) / 2;
                                    @endphp
                                    <p class="card-text">
                                        <strong>Préstamo:</strong> € {{ number_format($montoMedio, 2, ',', '.') }}<br>
                                        <strong>Plazo:</strong> 24 meses<br>
                                        <strong>Tasa:</strong> {{ $producto->tasa_interes }}%<br>
                                        <strong>Cuota mensual:</strong> € {{ number_format(($montoMedio * (($producto->tasa_interes/100)/12) * pow(1 + (($producto->tasa_interes/100)/12), 24)) / (pow(1 + (($producto->tasa_interes/100)/12), 24) - 1), 2, ',', '.') }}<br>
                                        <strong>Total a pagar:</strong> € {{ number_format((($montoMedio * (($producto->tasa_interes/100)/12) * pow(1 + (($producto->tasa_interes/100)/12), 24)) / (pow(1 + (($producto->tasa_interes/100)/12), 24) - 1)) * 24, 2, ',', '.') }}
                                    </p>
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