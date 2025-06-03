@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Solicitudes de Crédito</h4>
                    <a href="{{ route('credit.create') }}" class="btn btn-light">
                        <i class="fa fa-plus"></i> Nueva Solicitud
                    </a>
                </div>
                <div class="card-body">
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
                                        <td colspan="9" class="text-center">No hay solicitudes de crédito registradas</td>
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