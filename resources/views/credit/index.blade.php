@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Gestión de Créditos</h4>
                </div>
                <div class="card-body">
                    <!-- Selector de Funciones -->
                    <div class="mb-4">
                        <h5>Funciones Disponibles</h5>
                        @php
                        $creditFunctions = json_encode([
                            [
                                'id' => 'new_credit',
                                'name' => 'Nuevo Crédito',
                                'icon' => 'fa-plus-circle',
                                'description' => 'Crear un nuevo crédito',
                                'action' => route('credit.create')
                            ],
                            [
                                'id' => 'pending_approval',
                                'name' => 'Pendientes',
                                'icon' => 'fa-clock',
                                'description' => 'Créditos pendientes de aprobación',
                                'action' => route('credit.pending_approval')
                            ],
                            [
                                'id' => 'active_credits',
                                'name' => 'Activos',
                                'icon' => 'fa-check-circle',
                                'description' => 'Créditos activos',
                                'action' => '#active_credits'
                            ],
                            [
                                'id' => 'overdue_credits',
                                'name' => 'Vencidos',
                                'icon' => 'fa-exclamation-circle',
                                'description' => 'Créditos con pagos vencidos',
                                'action' => '#overdue_credits'
                            ],
                            [
                                'id' => 'payment_register',
                                'name' => 'Registrar Pago',
                                'icon' => 'fa-hand-holding-usd',
                                'description' => 'Registrar un nuevo pago',
                                'action' => route('payment.create')
                            ]
                        ]);
                        @endphp
                        
                        <x-function-selector 
                            id="credit-functions" 
                            :functions="$creditFunctions" 
                            theme="light"
                            onSelectCallback="
                                if (func.id === 'active_credits') {
                                    document.querySelector('#active-credits-section').scrollIntoView({behavior: 'smooth'});
                                }
                                if (func.id === 'overdue_credits') {
                                    document.querySelector('#overdue-credits-section').scrollIntoView({behavior: 'smooth'});
                                }
                                console.log('Función seleccionada:', func.name);
                            "
                        />
                    </div>
                    
                    <!-- Resto del contenido -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Monto + Interés</th>
                                    <th>Cuotas</th>
                                    <th>Frecuencia</th>
                                    <th>Estado</th>
                                    <th>Aprobación</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($credits) > 0)
                                    @foreach($credits as $credit)
                                        <tr>
                                            <td>{{ $credit->id }}</td>
                                            <td>
                                                @if($credit->user)
                                                    {{ $credit->user->name }} {{ $credit->user->last_name ?? '' }}
                                                @else
                                                    Cliente no disponible
                                                @endif
                                            </td>
                                            <td>${{ number_format($credit->amount, 2) }}</td>
                                            <td>${{ number_format($credit->amount_neto, 2) }}</td>
                                            <td>{{ $credit->payment_number }}</td>
                                            <td>{{ ucfirst($credit->payment_frequency) }}</td>
                                            <td>
                                                @if($credit->status == 'inprogress')
                                                    <span class="badge bg-primary">En Progreso</span>
                                                @elseif($credit->status == 'completed')
                                                    <span class="badge bg-success">Completado</span>
                                                @elseif($credit->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelado</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $credit->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($credit->approval_status == 'pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @elseif($credit->approval_status == 'aprobado')
                                                    <span class="badge bg-success">Aprobado</span>
                                                    @if($credit->approver)
                                                        <small class="d-block text-muted">por {{ $credit->approver->name }}</small>
                                                    @endif
                                                @elseif($credit->approval_status == 'rechazado')
                                                    <span class="badge bg-danger">Rechazado</span>
                                                    @if($credit->approver)
                                                        <small class="d-block text-muted">por {{ $credit->approver->name }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('credit.show', $credit->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    @if($credit->status == 'inprogress')
                                                        <a href="{{ route('credit.edit', $credit->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->hasRole('supervisor') || Auth::user()->hasRole('admin'))
                                                        @if($credit->approval_status == 'pendiente')
                                                            <a href="{{ route('credit.approval.form', $credit->id) }}" class="btn btn-sm btn-primary" title="Revisar aprobación">
                                                                <i class="fa fa-clipboard-check"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                    <form action="{{ route('credit.destroy', $credit->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar esta solicitud de crédito?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">No hay solicitudes de crédito registradas</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $credits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 