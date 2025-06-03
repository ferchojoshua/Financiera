@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
                    <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Caja</h4>
                    <div>
                        <a href="{{ url('/caja/create') }}" class="btn btn-outline-light btn-sm">
                            <i class="fa fa-plus"></i> Nuevo Movimiento
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Resumen de Caja -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Base Total</h5>
                                    <h3 class="card-text">{{ number_format($sum, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Carteras</h5>
                                    <h3 class="card-text">{{ count($clients) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ingresos Hoy</h5>
                                    <h3 class="card-text">{{ number_format($ingresos_hoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Egresos Hoy</h5>
                                    <h3 class="card-text">{{ number_format($egresos_hoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs para movimientos de caja -->
                    <ul class="nav nav-tabs mb-4" id="cashTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="wallets-tab" data-toggle="tab" href="#wallets" role="tab" aria-controls="wallets" aria-selected="true">
                                <i class="fa fa-wallet"></i> Carteras
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="movements-tab" data-toggle="tab" href="#movements" role="tab" aria-controls="movements" aria-selected="false">
                                <i class="fa fa-exchange-alt"></i> Movimientos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="reports-tab" data-toggle="tab" href="#reports" role="tab" aria-controls="reports" aria-selected="false">
                                <i class="fa fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                    </ul>

                    <!-- Contenido de las tabs -->
                    <div class="tab-content" id="cashTabContent">
                        <!-- Tab de Carteras -->
                        <div class="tab-pane fade show active" id="wallets" role="tabpanel" aria-labelledby="wallets-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cartera</th>
                                            <th>Base</th>
                                            <th>Cantidad Clientes</th>
                                            <th>Último Cierre</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                        @forelse($clients as $client)
                                            <tr>
                                                <td>{{ $client->id }}</td>
                                                <td>{{ $client->name }}</td>
                                                <td>{{ number_format($client->base, 2) }}</td>
                                                <td>{{ $client->number_of_clients ?? 0 }}</td>
                                                <td>{{ $client->last_close ?? 'Sin cierres' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#deposit-modal-{{ $client->id }}">
                                                            <i class="fa fa-plus"></i> Ingreso
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#withdraw-modal-{{ $client->id }}">
                                                            <i class="fa fa-minus"></i> Egreso
                                                        </button>
                                                    </div>

                                                    <!-- Modal de Depósito -->
                                                    <div class="modal fade" id="deposit-modal-{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel-{{ $client->id }}" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title" id="depositModalLabel-{{ $client->id }}">Ingreso a Cartera: {{ $client->name }}</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('supervisor.cash.income') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="wallet_id" value="{{ $client->id }}">
                                                                        <div class="form-group">
                                                                            <label for="amount">Monto:</label>
                                                                            <div class="input-group">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text">$</span>
                                                                                </div>
                                                                                <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="description">Descripción:</label>
                                                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <button type="submit" class="btn btn-success">
                                                                                <i class="fa fa-save"></i> Guardar
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal de Retiro -->
                                                    <div class="modal fade" id="withdraw-modal-{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel-{{ $client->id }}" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title" id="withdrawModalLabel-{{ $client->id }}">Egreso de Cartera: {{ $client->name }}</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('supervisor.cash.expense') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="wallet_id" value="{{ $client->id }}">
                                                                        <div class="form-group">
                                                                            <label for="amount">Monto:</label>
                                                                            <div class="input-group">
                                                                                <div class="input-group-prepend">
                                                                                    <span class="input-group-text">$</span>
                                                                                </div>
                                                                                <input type="number" step="0.01" min="0.01" max="{{ $client->base }}" class="form-control" id="amount" name="amount" required>
                                                                            </div>
                                                                            <small class="form-text text-muted">Máximo disponible: ${{ number_format($client->base, 2) }}</small>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="description">Descripción:</label>
                                                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <button type="submit" class="btn btn-danger">
                                                                                <i class="fa fa-save"></i> Guardar
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No hay carteras asignadas</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab de Movimientos -->
                        <div class="tab-pane fade" id="movements" role="tabpanel" aria-labelledby="movements-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                    <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Descripción</th>
                                            <th>Cartera</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($report as $r)
                                            <tr>
                                                <td>{{ $r->id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($r->type == 'ingreso')
                                                        <span class="badge bg-success">Ingreso</span>
                                                    @else
                                                        <span class="badge bg-danger">Egreso</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($r->amount, 2) }}</td>
                                                <td>{{ $r->description }}</td>
                                                <td>{{ $r->wallet_name ?? 'N/A' }}</td>
                                </tr>
                                        @empty
                                    <tr>
                                                <td colspan="6" class="text-center">No hay movimientos registrados</td>
                                    </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab de Reportes -->
                        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Reportes de Caja</h5>
                                    
                                    <form action="{{ route('caja.index') }}" method="GET" class="mb-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date_from">Fecha Desde:</label>
                                                    <input type="text" class="form-control datepicker" id="date_from" name="date_from" value="{{ request('date_from', date('d/m/Y', strtotime('-7 days'))) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date_to">Fecha Hasta:</label>
                                                    <input type="text" class="form-control datepicker" id="date_to" name="date_to" value="{{ request('date_to', date('d/m/Y')) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="wallet_id">Cartera:</label>
                                                    <select name="wallet_id" id="wallet_id" class="form-control">
                                                        <option value="">Todas las carteras</option>
                                                        @foreach($clients as $client)
                                                            <option value="{{ $client->id }}" {{ request('wallet_id') == $client->id ? 'selected' : '' }}>
                                                                {{ $client->name }}
                                                            </option>
                                @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Filtrar
                                            </button>
                                            <a href="{{ route('caja.index') }}" class="btn btn-secondary">
                                                <i class="fa fa-undo"></i> Reiniciar
                                            </a>
                                        </div>
                                    </form>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center">Ingresos vs Egresos</h5>
                                                    <div class="chart-container" style="height: 250px;">
                                                        <canvas id="incomeExpenseChart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-center">Movimientos por Cartera</h5>
                                                    <div class="chart-container" style="height: 250px;">
                                                        <canvas id="walletMovementsChart"></canvas>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
        
        // Gráfico de Ingresos vs Egresos (ejemplo)
        var incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
        var incomeExpenseChart = new Chart(incomeExpenseCtx, {
            type: 'bar',
            data: {
                labels: ['Ingresos', 'Egresos'],
                datasets: [{
                    label: 'Monto',
                    data: [
                        {{ $ingresos_total ?? 0 }}, 
                        {{ $egresos_total ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Gráfico de Movimientos por Cartera (ejemplo)
        var walletNames = [
            @foreach($clients as $client)
                '{{ $client->name }}',
            @endforeach
        ];
        
        var walletAmounts = [
            @foreach($clients as $client)
                {{ $client->base }},
            @endforeach
        ];
        
        var walletMovementsCtx = document.getElementById('walletMovementsChart').getContext('2d');
        var walletMovementsChart = new Chart(walletMovementsCtx, {
            type: 'pie',
            data: {
                labels: walletNames,
                datasets: [{
                    data: walletAmounts,
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(0, 123, 255, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(23, 162, 184, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection
