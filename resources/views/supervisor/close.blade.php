@extends('layouts.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Cierre Diario</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Gestione los cierres diarios de los agentes.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ url('supervisor/close/filter') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="agent_filter">Agente</label>
                            <select name="agent_id" id="agent_filter" class="form-select">
                                <option value="">Todos los agentes</option>
                                @foreach(\App\Models\User::where('level', 'agent')->orderBy('name')->get() as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="date_filter">Fecha</label>
                            <input type="date" name="date" id="date_filter" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#closeAllModal">
                        <i class="fa fa-check-circle"></i> Cierre Masivo
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Agente</th>
                            <th>Base</th>
                            <th>Cobros</th>
                            <th>Gastos</th>
                            <th>Balance</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
                        $agentFilter = isset($_GET['agent_id']) ? $_GET['agent_id'] : null;
                        
                        $agents = \App\Models\User::where('level', 'agent')
                            ->when($agentFilter, function($query) use ($agentFilter) {
                                return $query->where('id', $agentFilter);
                            })
                            ->orderBy('name')
                            ->get();
                            
                        $today = date('Y-m-d');
                        @endphp
                        
                        @if(count($agents) > 0)
                            @foreach($agents as $agent)
                                @php
                                $wallet = \App\Models\Wallet::where('id_agent', $agent->id)->first();
                                $base = $wallet ? $wallet->base : 0;
                                
                                $summary = \App\Models\Summary::where('id_agent', $agent->id)
                                    ->whereDate('created_at', $date)
                                    ->sum('amount');
                                    
                                $bills = \App\Models\Bill::where('id_agent', $agent->id)
                                    ->whereDate('created_at', $date)
                                    ->sum('amount');
                                    
                                $balance = $base + $summary - $bills;
                                
                                $closeDayExists = \App\Models\CloseDay::where('id_agent', $agent->id)
                                    ->whereDate('created_at', $date)
                                    ->exists();
                                @endphp
                                <tr>
                                    <td>{{ $agent->name }}</td>
                                    <td>${{ number_format($base, 2) }}</td>
                                    <td>${{ number_format($summary, 2) }}</td>
                                    <td>${{ number_format($bills, 2) }}</td>
                                    <td>${{ number_format($balance, 2) }}</td>
                                    <td>
                                        @if($closeDayExists)
                                            <span class="badge bg-success">Cerrado</span>
                                        @else
                                            <span class="badge bg-warning">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$closeDayExists)
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#closeModal{{ $agent->id }}">
                                                <i class="fa fa-lock"></i> Cerrar
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                <i class="fa fa-check"></i> Cerrado
                                            </button>
                                        @endif
                                        
                                        <!-- Modal de cierre individual -->
                                        <div class="modal fade" id="closeModal{{ $agent->id }}" tabindex="-1" aria-labelledby="closeModalLabel{{ $agent->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="closeModalLabel{{ $agent->id }}">Cierre Diario - {{ $agent->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ url('supervisor/close/store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                                                        <input type="hidden" name="date" value="{{ $date }}">
                                                        
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <p><strong>Base:</strong> ${{ number_format($base, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>Cobros:</strong> ${{ number_format($summary, 2) }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <p><strong>Gastos:</strong> ${{ number_format($bills, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>Balance:</strong> ${{ number_format($balance, 2) }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="new_base{{ $agent->id }}" class="form-label">Nueva Base</label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">$</span>
                                                                    <input type="number" step="0.01" name="new_base" id="new_base{{ $agent->id }}" class="form-control" value="{{ $balance }}">
                                                                </div>
                                                                <small class="form-text text-muted">Este será el monto base para mañana</small>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="notes{{ $agent->id }}" class="form-label">Observaciones</label>
                                                                <textarea name="notes" id="notes{{ $agent->id }}" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Confirmar Cierre</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No hay datos disponibles</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de cierre masivo -->
<div class="modal fade" id="closeAllModal" tabindex="-1" aria-labelledby="closeAllModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeAllModalLabel">Cierre Masivo Diario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('supervisor/close/all') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Esta acción cerrará el día para todos los agentes pendientes. ¿Está seguro?
                    </div>
                    
                    <div class="mb-3">
                        <label for="close_date" class="form-label">Fecha de Cierre</label>
                        <input type="date" name="close_date" id="close_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="global_notes" class="form-label">Observaciones Generales</label>
                        <textarea name="global_notes" id="global_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Cierre Masivo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 