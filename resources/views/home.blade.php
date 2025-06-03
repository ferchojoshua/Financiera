@extends('layouts.app')

@section('admin-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección admin-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('agent-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección agent-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('supervisor-section')
    <div class="col-md-12 mb-4">
        <div class="user-welcome bg-info text-white p-3 rounded">
            <h3><i class="fa fa-user-circle"></i> Bienvenido, {{ Auth::user()->name }}</h3>
            <p class="mb-0">{{ ucfirst(Auth::user()->level) }} | Último acceso: {{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- La sección supervisor-section está vacía ahora porque todas las tarjetas se mueven al menú lateral -->
@endsection

@section('agent-resume')
    @if(isset($base_agent))
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">DISPONIBLE (CAJA)</h5>
                        <h3 class="mb-0">
                            ${{ number_format($base_agent - $total_bill, 2) }}
                            @if($total_summary > 0)
                                <small class="text-white">
                                    + ${{ number_format($total_summary, 2) }} = ${{ number_format(($base_agent - $total_bill) + $total_summary, 2) }}
                                </small>
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL COBRADO</h5>
                        <h3 class="mb-0">${{ number_format($total_summary, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL GASTOS</h5>
                        <h3 class="mb-0">${{ number_format($total_bill, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('content')
    @if($close_day)
        <div class="alert alert-warning">
            <strong>¡Atención!</strong> Ya has realizado el cierre del día.
        </div>
    @else
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">TOTAL RECUPERADO</h5>
                            <h3 class="mb-0">${{ number_format($totalRecuperado, 2) }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('client/morosos') }}" class="text-decoration-none">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">MOROSOS</h5>
                            <h3 class="mb-0">{{ $totalMorosos }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">% RECUPERACIÓN</h5>
                            <h3 class="mb-0">{{ $porcentajeRecuperacion }}</h3>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="{{ url('client') }}" class="text-decoration-none">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">CLIENTES ACTIVOS</h5>
                            <h3 class="mb-0">{{ $clientesActivos }}</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Comparativa Recuperado vs Desembolsado</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center mb-3">
                                    <h6>Total Desembolsado</h6>
                                    <h3>${{ number_format($totalDesembolsado, 2) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center mb-3">
                                    <h6>Total Recuperado</h6>
                                    <h3>${{ number_format($totalRecuperado, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="progress" style="height: 25px;">
                            @php
                                $porcentaje = $totalDesembolsado > 0 ? ($totalRecuperado / $totalDesembolsado) * 100 : 0;
                                $porcentaje = min($porcentaje, 100); // Limitar a 100% máximo
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentaje }}%;" 
                                aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($porcentaje, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Resumen de Cobranza</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6>Préstamos Activos</h6>
                                    <h3>{{ $clientesActivos }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6>Préstamos en Mora</h6>
                                    <h3>{{ $totalMorosos }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6>Porcentaje de Mora</h6>
                                    <h3>{{ $clientesActivos > 0 ? round(($totalMorosos / $clientesActivos) * 100, 1) : 0 }}%</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Últimos Pagos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($ultimosPagos) > 0)
                                        @foreach($ultimosPagos as $pago)
                                        <tr>
                                            <td>{{ $pago->cliente }}</td>
                                            <td>${{ number_format($pago->monto, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">No hay pagos registrados</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Morosos Recientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Días Atraso</th>
                                        <th>Monto Pendiente</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($morososRecientes) > 0)
                                        @foreach($morososRecientes as $moroso)
                                        <tr>
                                            <td>{{ $moroso->cliente }}</td>
                                            <td>{{ $moroso->dias_atraso }}</td>
                                            <td>${{ number_format($moroso->monto_pendiente, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">No hay clientes morosos</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

<style>
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding: 15px 20px;
}

.card-body {
    padding: 20px;
}

.table th {
    border-top: none;
    color: #6c757d;
    font-weight: 500;
}

.table td {
    vertical-align: middle;
}

.opacity-50 {
    opacity: 0.5;
}

.fa-3x {
    font-size: 2.5em;
}
</style>
