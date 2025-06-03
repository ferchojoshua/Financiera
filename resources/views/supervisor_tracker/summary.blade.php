@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
                    <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Resumen de Actividad del Agente</h4>
                    <a href="{{ route('tracker.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Pagos</h5>
                                    <h3 class="card-text">{{ number_format($total_summary, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Créditos Nuevos</h5>
                                    <h3 class="card-text">{{ count($credit) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Monto Créditos</h5>
                                    <h3 class="card-text">{{ number_format($total_credit, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Facturas</h5>
                                    <h3 class="card-text">{{ count($bills) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs de navegación -->
                    <ul class="nav nav-tabs mb-4" id="activityTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="payments-tab" data-toggle="tab" href="#payments" role="tab" aria-controls="payments" aria-selected="true">
                                <i class="fa fa-money-bill"></i> Pagos Recibidos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="credits-tab" data-toggle="tab" href="#credits" role="tab" aria-controls="credits" aria-selected="false">
                                <i class="fa fa-credit-card"></i> Créditos Nuevos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="bills-tab" data-toggle="tab" href="#bills" role="tab" aria-controls="bills" aria-selected="false">
                                <i class="fa fa-file-invoice"></i> Facturas
                            </a>
                        </li>
                    </ul>

                    <!-- Contenido de las tabs -->
                    <div class="tab-content" id="activityTabContent">
                        <!-- Tab de Pagos -->
                        <div class="tab-pane fade show active" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Crédito</th>
                                            <th>Cuota</th>
                                            <th>Número</th>
                                            <th>Monto</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                        @forelse($summary as $sum)
                                            <tr>
                                                <td>{{ $sum->name }} {{ $sum->last_name }}</td>
                                                <td>{{ $sum->id_credit }}</td>
                                                <td>{{ $sum->number_index }} / {{ $sum->payment_number }}</td>
                                                <td>{{ $sum->number_index }}</td>
                                                <td>{{ number_format($sum->amount, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($sum->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('summary.show', $sum->id_credit) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No hay pagos registrados en esta fecha</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab de Créditos -->
                        <div class="tab-pane fade" id="credits" role="tabpanel" aria-labelledby="credits-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th>ID</th>
                                            <th>Provincia</th>
                                            <th>Monto</th>
                                            <th>Interés</th>
                                    <th>Cuotas</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($credit as $cred)
                                            <tr>
                                                <td>{{ $cred->name }} {{ $cred->last_name }}</td>
                                                <td>{{ $cred->credit_id }}</td>
                                                <td>{{ $cred->province }}</td>
                                                <td>{{ number_format($cred->amount_neto, 2) }}</td>
                                                <td>{{ number_format($cred->utility, 2) }}</td>
                                                <td>{{ $cred->payment_number }}</td>
                                                <td>{{ \Carbon\Carbon::parse($cred->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('credit.show', $cred->credit_id) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">No hay créditos registrados en esta fecha</td>
                                    </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                    </div>

                        <!-- Tab de Facturas -->
                        <div class="tab-pane fade" id="bills" role="tabpanel" aria-labelledby="bills-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Descripción</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                        @forelse($bills as $bill)
                                            <tr>
                                                <td>{{ $bill->id }}</td>
                                                <td>
                                                    @if($bill->type == 'receipt')
                                                        <span class="badge bg-success">Recibo</span>
                                                    @else
                                                        <span class="badge bg-warning">Otro</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($bill->amount, 2) }}</td>
                                                <td>{{ $bill->description }}</td>
                                                <td>{{ \Carbon\Carbon::parse($bill->created_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('bill.show', $bill->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </td>
                                </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No hay facturas registradas en esta fecha</td>
                                    </tr>
                                        @endforelse
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
@endsection
