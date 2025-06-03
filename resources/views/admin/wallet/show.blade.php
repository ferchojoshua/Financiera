@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalle de Billetera</h5>
                    <div>
                        <a href="{{ route('admin.wallet.edit', $wallet->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Información de la Billetera</h5>
                                    <hr>
                                    <p><strong>ID:</strong> {{ $wallet->id }}</p>
                                    <p><strong>Usuario:</strong> {{ $wallet->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $wallet->user->email }}</p>
                                    <p><strong>Descripción:</strong> {{ $wallet->description ?? 'N/A' }}</p>
                                    <p><strong>Fecha de Creación:</strong> {{ $wallet->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Saldo Actual</h5>
                                    <hr>
                                    <h2 class="display-4 text-center">${{ number_format($wallet->balance, 2) }}</h2>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#depositModal">
                                            <i class="fa fa-plus"></i> Depositar
                                        </button>
                                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                            <i class="fa fa-minus"></i> Retirar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Historial de Transacciones</h5>
                    @if(count($wallet->transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Realizado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wallet->transactions()->orderBy('created_at', 'desc')->get() as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                @if($transaction->type == 'deposit')
                                                    <span class="badge bg-success">Depósito</span>
                                                @elseif($transaction->type == 'withdrawal')
                                                    <span class="badge bg-danger">Retiro</span>
                                                @elseif($transaction->type == 'transfer_in')
                                                    <span class="badge bg-info">Transferencia Recibida</span>
                                                @elseif($transaction->type == 'transfer_out')
                                                    <span class="badge bg-warning">Transferencia Enviada</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($transaction->amount, 2) }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $transaction->creator ? $transaction->creator->name : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay transacciones registradas para esta billetera.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Depósito -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wallet.deposit', $wallet->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Realizar Depósito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="deposit_amount" class="form-label">Monto a Depositar</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="deposit_amount" name="amount" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="deposit_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="deposit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Depositar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Retiro -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.wallet.withdraw', $wallet->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Realizar Retiro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="withdraw_amount" class="form-label">Monto a Retirar</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $wallet->balance }}" class="form-control" id="withdraw_amount" name="amount" required>
                        </div>
                        <small class="form-text text-muted">Saldo disponible: ${{ number_format($wallet->balance, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label for="withdraw_description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="withdraw_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Retirar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 