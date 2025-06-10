@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Reporte de Préstamos Cancelados</h4>
                        <div>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <div class="btn-group ml-2">
                                <a href="{{ route('reports.export', ['type' => 'cancelled', 'format' => 'excel']) }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-file-excel"></i> Excel
                                </a>
                                <a href="{{ route('reports.export', ['type' => 'cancelled', 'format' => 'pdf']) }}" class="btn btn-outline-light btn-sm">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('reports.cancelled') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="start_date" class="mr-2 form-label">Desde:</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate ?? date('Y-m-01') }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="end_date" class="mr-2 form-label">Hasta:</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate ?? date('Y-m-d') }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="user_id" class="mr-2 form-label">Cliente:</label>
                                    <select id="user_id" name="user_id" class="form-control">
                                        <option value="">Todos los clientes</option>
                                        @foreach($users ?? [] as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} {{ $user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('reports.cancelled') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-sync"></i> Reiniciar
                                </a>
                            </form>
                        </div>
                    </div>

                    <div class="table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Tasa</th>
                                    <th>Fecha Otorgamiento</th>
                                    <th>Fecha Cancelación</th>
                                    <th>Duración</th>
                                    <th>Interés Pagado</th>
                                    <th>Total Pagado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credits ?? [] as $credit)
                                    <tr>
                                        <td>{{ $credit->id }}</td>
                                        <td>
                                            <a href="{{ route('clients.show', $credit->user_id) }}" class="text-primary">
                                                {{ $credit->user->name ?? 'N/A' }} {{ $credit->user->last_name ?? '' }}
                                            </a>
                                        </td>
                                        <td class="text-right">${{ number_format($credit->amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($credit->interest_rate, 2) }}%</td>
                                        <td>{{ $credit->disbursement_date ? date('d/m/Y', strtotime($credit->disbursement_date)) : 'N/A' }}</td>
                                        <td>{{ $credit->cancellation_date ? date('d/m/Y', strtotime($credit->cancellation_date)) : 'N/A' }}</td>
                                        <td>
                                            @if($credit->disbursement_date && $credit->cancellation_date)
                                                {{ \Carbon\Carbon::parse($credit->disbursement_date)->diffInDays(\Carbon\Carbon::parse($credit->cancellation_date)) }} días
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="text-right">${{ number_format($credit->interest_paid ?? 0, 2) }}</td>
                                        <td class="text-right">${{ number_format($credit->total_paid ?? 0, 2) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('credits.show', $credit->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('payments.index', ['credit_id' => $credit->id]) }}" class="btn btn-sm btn-success" title="Ver pagos">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="empty-state">
                                                <i class="fas fa-search empty-icon"></i>
                                                <p>No hay préstamos cancelados en el período seleccionado</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-right">Totales:</td>
                                    <td class="text-right">${{ number_format($credits->sum('amount') ?? 0, 2) }}</td>
                                    <td></td>
                                    <td colspan="3"></td>
                                    <td class="text-right">${{ number_format($credits->sum('interest_paid') ?? 0, 2) }}</td>
                                    <td class="text-right">${{ number_format($credits->sum('total_paid') ?? 0, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $credits->appends(request()->except('page'))->links() }}
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header primary">
                                    <h5 class="mb-0">Resumen</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Total de préstamos cancelados:</span>
                                            <span class="badge bg-primary text-white">{{ $credits->total() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Monto total desembolsado:</span>
                                            <span class="badge bg-primary text-white">${{ number_format($credits->sum('amount') ?? 0, 2) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Interés total pagado:</span>
                                            <span class="badge bg-success text-white">${{ number_format($credits->sum('interest_paid') ?? 0, 2) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>Total recaudado:</span>
                                            <span class="badge bg-success text-white">${{ number_format($credits->sum('total_paid') ?? 0, 2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header primary">
                                    <h5 class="mb-0">Estadísticas</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="cancelledChart" width="400" height="250"></canvas>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Configuración de gráfico si hay datos
        @if(isset($credits) && $credits->count() > 0)
        var ctx = document.getElementById('cancelledChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Principal', 'Interés', 'Total'],
                datasets: [{
                    label: 'Montos (en moneda local)',
                    data: [
                        {{ $credits->sum('amount') ?? 0 }},
                        {{ $credits->sum('interest_paid') ?? 0 }},
                        {{ $credits->sum('total_paid') ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(14, 135, 114, 0.6)',
                        'rgba(41, 128, 185, 0.6)',
                        'rgba(39, 174, 96, 0.6)'
                    ],
                    borderColor: [
                        'rgba(14, 135, 114, 1)',
                        'rgba(41, 128, 185, 1)',
                        'rgba(39, 174, 96, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Relación Principal vs Interés'
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection 