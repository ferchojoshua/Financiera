@extends('layouts.app')

@section('content')
    <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main">
        <div class="wrap">
            <section class="app-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title">Cierre Diario</h4>
                            </header><!-- .widget-header -->
                            <hr class="widget-separator">
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form action="{{ url('supervisor/daily-close/filter') }}" method="GET" class="form-inline mb-4">
                                            <div class="form-group mr-3">
                                                <label for="agent_filter" class="mr-2">Agente:</label>
                                                <select name="agent_id" id="agent_filter" class="form-control">
                                                    <option value="">Todos los agentes</option>
                                                    @foreach(\App\User::where('role', 'agent')->orderBy('name')->get() as $agent)
                                                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }} {{ $agent->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mr-3">
                                                <label for="date_filter" class="mr-2">Fecha:</label>
                                                <input type="date" name="date" id="date_filter" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Filtrar
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#closeAllModal">
                                            <i class="fa fa-check-circle"></i> Cierre Masivo
                                        </button>
                                    </div>
                                </div>

                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

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
                                        $date = request('date', date('Y-m-d'));
                                        $agentFilter = request('agent_id');
                                        
                                        $agents = \App\User::where('role', 'agent')
                                            ->when($agentFilter, function($query) use ($agentFilter) {
                                                return $query->where('id', $agentFilter);
                                            })
                                            ->orderBy('name')
                                            ->get();
                                        @endphp
                                        
                                        @foreach($agents as $agent)
                                            @php
                                            // Obtener base del agente
                                            $agentBase = \App\db_supervisor_has_agent::where('id_user_agent', $agent->id)
                                                ->first();
                                                
                                            $base = $agentBase ? $agentBase->base : 0;
                                            
                                            // Obtener cobros del día
                                            $collections = \App\db_summary::where('id_agent', $agent->id)
                                                ->whereDate('created_at', $date)
                                                ->sum('amount');
                                                
                                            // Gastos del día (simulado, reemplazar con tabla real)
                                            $expenses = \DB::table('bills')
                                                ->where('id_agent', $agent->id)
                                                ->whereDate('created_at', $date)
                                                ->sum('amount');
                                                
                                            // Balance
                                            $balance = $base + $collections - $expenses;
                                            
                                            // Verificar si ya existe cierre
                                            $isClosed = \DB::table('close_day')
                                                ->where('id_agent', $agent->id)
                                                ->whereDate('created_at', $date)
                                                ->exists();
                                            @endphp
                                            <tr>
                                                <td>{{ $agent->name }} {{ $agent->last_name }}</td>
                                                <td>${{ number_format($base, 2) }}</td>
                                                <td>${{ number_format($collections, 2) }}</td>
                                                <td>${{ number_format($expenses, 2) }}</td>
                                                <td>${{ number_format($balance, 2) }}</td>
                                                <td>
                                                    @if($isClosed)
                                                        <span class="badge badge-success">Cerrado</span>
                                                    @else
                                                        <span class="badge badge-warning">Pendiente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!$isClosed)
                                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#closeModal{{ $agent->id }}">
                                                            <i class="fa fa-lock"></i> Cerrar
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                            <i class="fa fa-check"></i> Cerrado
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal para cierre individual -->
                                            <div class="modal fade" id="closeModal{{ $agent->id }}" tabindex="-1" role="dialog" aria-labelledby="closeModalLabel{{ $agent->id }}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="closeModalLabel{{ $agent->id }}">Cierre Diario - {{ $agent->name }} {{ $agent->last_name }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        </div>
                                                        <form action="{{ url('supervisor/daily-close/store') }}" method="POST">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                                                            <input type="hidden" name="date" value="{{ $date }}">
                                                            
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Base:</strong> ${{ number_format($base, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Cobros:</strong> ${{ number_format($collections, 2) }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Gastos:</strong> ${{ number_format($expenses, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Balance:</strong> ${{ number_format($balance, 2) }}</p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="new_base{{ $agent->id }}">Nueva Base</label>
                                                                    <input type="number" step="0.01" name="new_base" id="new_base{{ $agent->id }}" class="form-control" value="{{ $balance }}">
                                                                    <small class="form-text text-muted">Este será el monto base para mañana</small>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="notes{{ $agent->id }}">Observaciones</label>
                                                                    <textarea name="notes" id="notes{{ $agent->id }}" class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Confirmar Cierre</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div><!-- .widget-body -->
                        </div><!-- .widget -->
                    </div><!-- END column -->
                </div><!-- .row -->
            </section>
        </div>
    </main>
    
    <!-- Modal para cierre masivo -->
    <div class="modal fade" id="closeAllModal" tabindex="-1" role="dialog" aria-labelledby="closeAllModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="closeAllModalLabel">Cierre Masivo Diario</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ url('supervisor/daily-close/store-all') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> Esta acción cerrará el día para todos los agentes pendientes. ¿Está seguro?
                        </div>
                        
                        <div class="form-group">
                            <label for="close_date">Fecha de Cierre</label>
                            <input type="date" name="close_date" id="close_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="form-group">
                            <label for="global_notes">Observaciones Generales</label>
                            <textarea name="global_notes" id="global_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar Cierre Masivo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 