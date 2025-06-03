@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Revisión de Cartera</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Supervise y analice el estado de la cartera de créditos.
            </div>
            
            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ url('supervisor/review/filter') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="agent_filter">Agente</label>
                            <select name="agent_id" id="agent_filter" class="form-select">
                                <option value="">Todos los agentes</option>
                                @foreach(\App\Models\User::where('level', 'agent')->orderBy('name')->get() as $agent)
                                    <option value="{{ $agent->id }}" {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_filter">Estado</label>
                            <select name="status" id="status_filter" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="inprogress" {{ isset($_GET['status']) && $_GET['status'] == 'inprogress' ? 'selected' : '' }}>En progreso</option>
                                <option value="late" {{ isset($_GET['status']) && $_GET['status'] == 'late' ? 'selected' : '' }}>Atrasado</option>
                                <option value="legal" {{ isset($_GET['status']) && $_GET['status'] == 'legal' ? 'selected' : '' }}>Legal</option>
                                <option value="completed" {{ isset($_GET['status']) && $_GET['status'] == 'completed' ? 'selected' : '' }}>Completado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="search_filter">Buscar</label>
                            <input type="text" name="search" id="search_filter" class="form-control" placeholder="ID, Cliente o Teléfono" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <a href="{{ url('supervisor/review/export') }}" class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Exportar Reporte
                    </a>
                </div>
            </div>
            
            <!-- Resumen de Cartera -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            @php
                            $totalCreditos = \App\Models\Credit::count();
                            $totalMonto = \App\Models\Credit::sum('amount');
                            @endphp
                            <h3 class="mb-0">{{ $totalCreditos }}</h3>
                            <p class="mb-0">Créditos Totales</p>
                            <p class="mb-0">${{ number_format($totalMonto, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            @php
                            $creditosAlDia = \App\Models\Credit::where('status', 'inprogress')
                                           ->whereRaw('id NOT IN (SELECT id_credit FROM balance WHERE amount < 0)')
                                           ->count();
                            $montoAlDia = \App\Models\Credit::where('status', 'inprogress')
                                        ->whereRaw('id NOT IN (SELECT id_credit FROM balance WHERE amount < 0)')
                                        ->sum('amount');
                            @endphp
                            <h3 class="mb-0">{{ $creditosAlDia }}</h3>
                            <p class="mb-0">Al Día</p>
                            <p class="mb-0">${{ number_format($montoAlDia, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            @php
                            $creditosAtrasados = \App\Models\Credit::where('status', 'late')->count();
                            $montoAtrasados = \App\Models\Credit::where('status', 'late')->sum('amount');
                            @endphp
                            <h3 class="mb-0">{{ $creditosAtrasados }}</h3>
                            <p class="mb-0">Atrasados</p>
                            <p class="mb-0">${{ number_format($montoAtrasados, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            @php
                            $creditosLegales = \App\Models\Credit::where('status', 'legal')->count();
                            $montoLegales = \App\Models\Credit::where('status', 'legal')->sum('amount');
                            @endphp
                            <h3 class="mb-0">{{ $creditosLegales }}</h3>
                            <p class="mb-0">En Legal</p>
                            <p class="mb-0">${{ number_format($montoLegales, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de Créditos -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Agente</th>
                            <th>Monto</th>
                            <th>Pagado</th>
                            <th>Pendiente</th>
                            <th>Estado</th>
                            <th>Última Cuota</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $agentId = isset($_GET['agent_id']) ? $_GET['agent_id'] : null;
                        $status = isset($_GET['status']) ? $_GET['status'] : null;
                        $search = isset($_GET['search']) ? $_GET['search'] : null;
                        
                        $creditos = \App\Models\Credit::join('users', 'credits.id_user', '=', 'users.id')
                                   ->select('credits.*', 'users.name as client_name', 'users.phone')
                                   ->when($agentId, function($query) use ($agentId) {
                                       return $query->where('credits.id_agent', $agentId);
                                   })
                                   ->when($status, function($query) use ($status) {
                                       return $query->where('credits.status', $status);
                                   })
                                   ->when($search, function($query) use ($search) {
                                       return $query->where(function($q) use ($search) {
                                           $q->where('credits.id', 'like', "%{$search}%")
                                             ->orWhere('users.name', 'like', "%{$search}%")
                                             ->orWhere('users.phone', 'like', "%{$search}%");
                                       });
                                   })
                                   ->orderBy('credits.id', 'desc')
                                   ->paginate(10);
                        @endphp
                        
                        @if(count($creditos) > 0)
                            @foreach($creditos as $credito)
                                @php
                                $agent = \App\Models\User::find($credito->id_agent);
                                $pagado = \App\Models\Payment::where('id_credit', $credito->id)->sum('amount');
                                $pendiente = $credito->amount - $pagado;
                                $ultimoPago = \App\Models\Payment::where('id_credit', $credito->id)
                                              ->orderBy('created_at', 'desc')
                                              ->first();
                                @endphp
                                <tr>
                                    <td>{{ $credito->id }}</td>
                                    <td>
                                        {{ $credito->client_name }}
                                        <br>
                                        <small class="text-muted">{{ $credito->phone }}</small>
                                    </td>
                                    <td>{{ $agent ? $agent->name : 'Sin asignar' }}</td>
                                    <td>${{ number_format($credito->amount, 2) }}</td>
                                    <td>${{ number_format($pagado, 2) }}</td>
                                    <td>${{ number_format($pendiente, 2) }}</td>
                                    <td>
                                        @if($credito->status == 'inprogress')
                                            <span class="badge bg-success">Al día</span>
                                        @elseif($credito->status == 'late')
                                            <span class="badge bg-warning">Atrasado</span>
                                        @elseif($credito->status == 'legal')
                                            <span class="badge bg-danger">Legal</span>
                                        @elseif($credito->status == 'completed')
                                            <span class="badge bg-info">Completado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $credito->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ultimoPago)
                                            {{ \Carbon\Carbon::parse($ultimoPago->created_at)->format('d/m/Y') }}
                                        @else
                                            Sin pagos
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('supervisor/credit/'.$credito->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal{{ $credito->id }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        
                                        <!-- Modal para cambiar estado -->
                                        <div class="modal fade" id="statusModal{{ $credito->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $credito->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalLabel{{ $credito->id }}">Cambiar Estado - Crédito #{{ $credito->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ url('supervisor/credit/status/'.$credito->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="status{{ $credito->id }}" class="form-label">Estado</label>
                                                                <select name="status" id="status{{ $credito->id }}" class="form-select">
                                                                    <option value="inprogress" {{ $credito->status == 'inprogress' ? 'selected' : '' }}>Al día</option>
                                                                    <option value="late" {{ $credito->status == 'late' ? 'selected' : '' }}>Atrasado</option>
                                                                    <option value="legal" {{ $credito->status == 'legal' ? 'selected' : '' }}>Legal</option>
                                                                    <option value="completed" {{ $credito->status == 'completed' ? 'selected' : '' }}>Completado</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="notes{{ $credito->id }}" class="form-label">Notas</label>
                                                                <textarea name="notes" id="notes{{ $credito->id }}" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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
                                <td colspan="9" class="text-center">No hay créditos que coincidan con los filtros</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center">
                    {{ $creditos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 