@extends('layouts.app')
@section('title', 'Asignar Préstamos a Ruta')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Asignar Préstamos a Ruta: {{ $route->name }}</h4>
                <div class="card-tools">
                    <a href="{{ route('routes.show', $route->id) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('routes.save_assign_credits', $route->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title">Préstamos Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    @if($unassignedCredits->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">
                                                        <div class="form-check">
                                                            <input class="form-check-input select-all" type="checkbox" id="selectAllUnassigned">
                                                            <label class="form-check-label" for="selectAllUnassigned"></label>
                                                        </div>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>Monto</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($unassignedCredits as $credit)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input unassigned-credit" type="checkbox" name="credit_ids[]" value="{{ $credit->id }}" id="credit{{ $credit->id }}">
                                                            <label class="form-check-label" for="credit{{ $credit->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $credit->id }}</td>
                                                    <td>{{ $credit->user->name }} {{ $credit->user->last_name ?? '' }}</td>
                                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($credit->created_at)) }}</td>
                                                    <td>
                                                        @if($credit->is_overdue)
                                                            <span class="badge badge-danger">Vencido</span>
                                                        @else
                                                            <span class="badge badge-success">Al día</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info">
                                        No hay préstamos disponibles para asignar.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title">Préstamos Asignados a la Ruta</h5>
                                </div>
                                <div class="card-body">
                                    @if($assignedCredits->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">
                                                        <div class="form-check">
                                                            <input class="form-check-input select-all" type="checkbox" id="selectAllAssigned">
                                                            <label class="form-check-label" for="selectAllAssigned"></label>
                                                        </div>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>Monto</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assignedCredits as $credit)
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input assigned-credit" type="checkbox" name="credit_ids[]" value="{{ $credit->id }}" id="credit{{ $credit->id }}" checked>
                                                            <label class="form-check-label" for="credit{{ $credit->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $credit->id }}</td>
                                                    <td>{{ $credit->user->name }} {{ $credit->user->last_name ?? '' }}</td>
                                                    <td>{{ number_format($credit->amount, 2) }}</td>
                                                    <td>{{ date('d/m/Y', strtotime($credit->created_at)) }}</td>
                                                    <td>
                                                        @if($credit->is_overdue)
                                                            <span class="badge badge-danger">Vencido</span>
                                                        @else
                                                            <span class="badge badge-success">Al día</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-warning">
                                        No hay préstamos asignados a esta ruta.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Asignaciones
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Seleccionar todos los préstamos no asignados
        $('#selectAllUnassigned').change(function() {
            $('.unassigned-credit').prop('checked', $(this).prop('checked'));
        });
        
        // Seleccionar todos los préstamos asignados
        $('#selectAllAssigned').change(function() {
            $('.assigned-credit').prop('checked', $(this).prop('checked'));
        });
    });
</script>
@endpush
@endsection 