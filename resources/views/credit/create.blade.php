@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Nueva Solicitud de Crédito</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('credit.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_user">Cliente <span class="text-danger">*</span></label>
                                    <select id="id_user" name="id_user" class="form-control @error('id_user') is-invalid @enderror" required>
                                        <option value="">Seleccione un cliente</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('id_user') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }} {{ $client->last_name ?? '' }} - {{ $client->nit ?? 'Sin NIT' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_user')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_wallet">Cartera/Billetera <span class="text-danger">*</span></label>
                                    <select id="id_wallet" name="id_wallet" class="form-control @error('id_wallet') is-invalid @enderror" required>
                                        <option value="">Seleccione una cartera</option>
                                        @foreach($wallets as $wallet)
                                            <option value="{{ $wallet->id }}" {{ old('id_wallet') == $wallet->id ? 'selected' : '' }}>
                                                ID: {{ $wallet->id }} - Base: ${{ number_format($wallet->base, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_wallet')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amount">Monto del Préstamo <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required step="0.01" min="1">
                                    </div>
                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="utility">Interés (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" id="utility" name="utility" class="form-control @error('utility') is-invalid @enderror" value="{{ old('utility', 10) }}" required step="0.01" min="0">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('utility')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="period">Período (días) <span class="text-danger">*</span></label>
                                    <input type="number" id="period" name="period" class="form-control @error('period') is-invalid @enderror" value="{{ old('period', 30) }}" required min="1">
                                    @error('period')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_frequency">Frecuencia de Pago <span class="text-danger">*</span></label>
                                    <select id="payment_frequency" name="payment_frequency" class="form-control @error('payment_frequency') is-invalid @enderror" required>
                                        <option value="diario" {{ old('payment_frequency') == 'diario' ? 'selected' : '' }}>Diario</option>
                                        <option value="semanal" {{ old('payment_frequency') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                        <option value="quincenal" {{ old('payment_frequency') == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                                        <option value="mensual" {{ old('payment_frequency') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                    </select>
                                    @error('payment_frequency')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_number">Número de Pagos <span class="text-danger">*</span></label>
                                    <input type="number" id="payment_number" name="payment_number" class="form-control @error('payment_number') is-invalid @enderror" value="{{ old('payment_number', 30) }}" required min="1">
                                    @error('payment_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Monto Total con Interés</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="amount_neto" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Monto por Cuota</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="payment_amount" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="route_id">Ruta de Cobro</label>
                                    <select id="route_id" name="route_id" class="form-control @error('route_id') is-invalid @enderror">
                                        <option value="">Sin ruta asignada</option>
                                        @if(isset($routes))
                                            @foreach($routes as $route)
                                                <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                                    {{ $route->name }} - {{ $route->description ?? 'Sin descripción' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('route_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Solicitud
                            </button>
                            <a href="{{ route('credit.index') }}" class="btn btn-secondary ml-2">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular montos al cargar la página
    calculateAmounts();
    
    // Calcular montos cuando cambian los valores
    document.getElementById('amount').addEventListener('input', calculateAmounts);
    document.getElementById('utility').addEventListener('input', calculateAmounts);
    document.getElementById('payment_number').addEventListener('input', calculateAmounts);
    
    function calculateAmounts() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const utility = parseFloat(document.getElementById('utility').value) || 0;
        const paymentNumber = parseInt(document.getElementById('payment_number').value) || 1;
        
        // Calcular monto total con interés
        const amountNeto = amount + (amount * utility / 100);
        
        // Calcular monto por cuota
        const paymentAmount = amountNeto / paymentNumber;
        
        // Mostrar resultados
        document.getElementById('amount_neto').value = amountNeto.toFixed(2);
        document.getElementById('payment_amount').value = paymentAmount.toFixed(2);
    }
});
</script>
@endsection 