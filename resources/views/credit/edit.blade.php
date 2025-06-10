@extends('layouts.app')

@section('title', 'Editar Crédito')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Crédito #{{ $credit->id }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('credit.update', $credit->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Fila 1: Cliente y Billetera --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">Cliente:</label>
                                <select name="client_id" id="client_id" class="form-control" required>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $credit->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }} {{ $client->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="id_wallet" class="form-label">Billetera:</label>
                                <select name="id_wallet" id="id_wallet" class="form-control" required>
                                     @foreach($wallets as $wallet)
                                        <option value="{{ $wallet->id }}" {{ $credit->id_wallet == $wallet->id ? 'selected' : '' }}>
                                            Billetera #{{ $wallet->id }} 
                                            @if($wallet->user)
                                                - ({{ $wallet->user->name }})
                                            @elseif($wallet->description)
                                                - ({{ $wallet->description }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: Monto y Utilidad --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Monto del Crédito ($):</label>
                                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $credit->amount) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="utility" class="form-label">Utilidad (%):</label>
                                <input type="number" name="utility" id="utility" class="form-control" value="{{ old('utility', $credit->utility) }}" required>
                            </div>
                        </div>

                        {{-- Fila 3: Periodo y Frecuencia de Pago --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="period" class="form-label">Período (días):</label>
                                <input type="number" name="period" id="period" class="form-control" value="{{ old('period', $credit->period) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_frequency" class="form-label">Frecuencia de Pago:</label>
                                <select name="payment_frequency" id="payment_frequency" class="form-control" required>
                                    <option value="diario" {{ $credit->payment_frequency == 'diario' ? 'selected' : '' }}>Diario</option>
                                    <option value="semanal" {{ $credit->payment_frequency == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                    <option value="quincenal" {{ $credit->payment_frequency == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                    <option value="mensual" {{ $credit->payment_frequency == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                </select>
                            </div>
                        </div>

                        {{-- Fila 4: Número de Cuotas y Estado --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_number" class="form-label">Número de Cuotas:</label>
                                <input type="number" name="payment_number" id="payment_number" class="form-control" value="{{ old('payment_number', $credit->payment_number) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Estado:</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="inprogress" {{ $credit->status == 'inprogress' ? 'selected' : '' }}>En Progreso</option>
                                    <option value="completed" {{ $credit->status == 'completed' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelled" {{ $credit->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('credit.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar Crédito</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 