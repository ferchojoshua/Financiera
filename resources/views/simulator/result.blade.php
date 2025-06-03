@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4 no-print">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Resultado de la Simulación</h4>
            <div>
                <a href="{{ route('simulator.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Regresar
                </a>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fa fa-print"></i> Imprimir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Resumen del Préstamo</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Tipo de Préstamo:</th>
                                    <td>
                                        @if($summary['loan_type'] == 'personal')
                                            <span class="badge bg-info">Personal</span>
                                        @else
                                            <span class="badge bg-success">PYME</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Monto Solicitado:</th>
                                    <td>${{ number_format($summary['amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Plazo:</th>
                                    <td>{{ $summary['term'] }} meses</td>
                                </tr>
                                <tr>
                                    <th>Tasa de Interés:</th>
                                    <td>{{ number_format($summary['interest_rate'], 2) }}% anual</td>
                                </tr>
                                <tr>
                                    <th>Frecuencia de Pago:</th>
                                    <td>
                                        @switch($summary['payment_frequency'])
                                            @case('daily')
                                                Diario
                                                @break
                                            @case('weekly')
                                                Semanal
                                                @break
                                            @case('biweekly')
                                                Quincenal
                                                @break
                                            @default
                                                Mensual
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Número de Pagos:</th>
                                    <td>{{ $summary['total_payments'] }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <th>Cuota:</th>
                                    <td><strong>${{ number_format($summary['payment'], 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Total Intereses:</th>
                                    <td>${{ number_format($summary['total_interest'], 2) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <th>Monto Total a Pagar:</th>
                                    <td><strong>${{ number_format($summary['total_amount'], 2) }}</strong></td>
                                </tr>
                            </table>
                            
                            @if($summary['loan_type'] == 'pyme')
                                <div class="alert alert-success mt-3">
                                    <i class="fa fa-info-circle"></i> Los préstamos para PYMEs tienen una tasa preferencial con un 10% de descuento sobre la tasa estándar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Gráfico de Distribución</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div style="width: 100%; max-width: 300px;">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card print-only">
        <div class="card-header">
            <h4>Tabla de Amortización</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Pago #</th>
                            <th>Cuota</th>
                            <th>Capital</th>
                            <th>Interés</th>
                            <th>Balance</th>
                            <th>Interés Acumulado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedule as $payment)
                            <tr>
                                <td>{{ $payment['period'] }}</td>
                                <td>${{ number_format($payment['payment'], 2) }}</td>
                                <td>${{ number_format($payment['principal'], 2) }}</td>
                                <td>${{ number_format($payment['interest'], 2) }}</td>
                                <td>${{ number_format($payment['balance'], 2) }}</td>
                                <td>${{ number_format($payment['accumulated_interest'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-only, .print-only * {
            visibility: visible;
        }
        .print-only {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('pieChart');
        if (!ctx) return;
        
        // Datos para el gráfico
        const capital = {{ $summary['amount'] }};
        const interest = {{ $summary['total_interest'] }};
        
        const pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Capital', 'Intereses'],
                datasets: [{
                    data: [capital, interest],
                    backgroundColor: ['#36a2eb', '#ff6384'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('es-ES', { 
                                        style: 'currency', 
                                        currency: 'USD' 
                                    }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection 