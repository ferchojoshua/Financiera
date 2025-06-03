@extends('layouts.app')

@section('supervisor-section')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Asignación de Base a Agentes</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Desde aquí puede asignar base a los agentes bajo su supervisión.
            </div>
            
            <form action="{{ url('supervisor/agent/assign') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="agent">Seleccione Agente</label>
                        <select name="agent_id" id="agent" class="form-select">
                            <option value="">-- Seleccione un agente --</option>
                            @foreach(\App\Models\User::where('level', 'agent')->orderBy('name')->get() as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="amount">Monto Base</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <label for="notes">Notas/Observaciones</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Asignar Base
                    </button>
                </div>
            </form>
            
            <hr>
            
            <h5>Bases Asignadas</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Agente</th>
                            <th>Base Actual</th>
                            <th>Última Asignación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $wallets = \App\Models\Wallet::join('users', 'users.id', '=', 'wallets.user_id')
                                ->select('wallets.*', 'users.name')
                                ->where('wallets.type', 'deposit')
                                ->get();
                        @endphp
                        
                        @if(count($wallets) > 0)
                            @foreach($wallets as $wallet)
                            <tr>
                                <td>{{ $wallet->name }}</td>
                                <td>${{ number_format($wallet->amount, 2) }}</td>
                                <td>{{ $wallet->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $wallet->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    
                                    <!-- Modal para editar -->
                                    <div class="modal fade" id="editModal{{ $wallet->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $wallet->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel{{ $wallet->id }}">Editar Base de {{ $wallet->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ url('supervisor/agent/update/' . $wallet->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit_amount{{ $wallet->id }}" class="form-label">Monto Base</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" step="0.01" name="amount" id="edit_amount{{ $wallet->id }}" class="form-control" value="{{ $wallet->amount }}">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_notes{{ $wallet->id }}" class="form-label">Notas/Observaciones</label>
                                                            <textarea name="notes" id="edit_notes{{ $wallet->id }}" class="form-control" rows="3">{{ $wallet->description }}</textarea>
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
                                <td colspan="4" class="text-center">No hay bases asignadas</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 