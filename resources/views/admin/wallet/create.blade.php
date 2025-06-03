@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Crear Nueva Billetera</h5>
                    <a href="{{ url('/admin/wallet') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i> Volver a la lista
                    </a>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/admin/wallet') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">-- Seleccionar Usuario --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="initial_balance" class="form-label">Saldo Inicial <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">C$</span>
                                        <input type="number" step="0.01" min="0" class="form-control @error('initial_balance') is-invalid @enderror" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', 0) }}" required>
                                    </div>
                                    @error('initial_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Billetera
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(count($wallets) > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Billeteras Existentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Saldo</th>
                                    <th>Descripción</th>
                                    <th>Fecha Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wallets as $wallet)
                                <tr>
                                    <td>{{ $wallet->id }}</td>
                                    <td>{{ $wallet->user->name }}</td>
                                    <td>${{ number_format($wallet->balance, 2) }}</td>
                                    <td>{{ $wallet->description ?? 'N/A' }}</td>
                                    <td>{{ $wallet->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 