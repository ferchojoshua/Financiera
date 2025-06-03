@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Editar Acción de Cobranza</h5>
                        <a href="{{ route('collection.actions.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <p>Esta es una vista de implementación básica para editar una acción de cobranza. Esta vista se completará con la información real en futuras actualizaciones.</p>
                    </div>

                    <form action="{{ route('collection.actions.update', $id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="credit_id">Crédito <span class="text-danger">*</span></label>
                                <select name="credit_id" id="credit_id" class="form-control @error('credit_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar crédito</option>
                                    @foreach(\App\Models\Credit::where('status', 'active')->with('user')->get() as $credit)
                                        <option value="{{ $credit->id }}" {{ old('credit_id') == $credit->id ? 'selected' : '' }}>
                                            #{{ $credit->id }} - {{ $credit->user->name ?? 'Cliente sin nombre' }} - ${{ number_format($credit->amount_neto, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('credit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="action_type">Tipo de Acción <span class="text-danger">*</span></label>
                                <select name="action_type" id="action_type" class="form-control @error('action_type') is-invalid @enderror" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="llamada" {{ old('action_type') == 'llamada' ? 'selected' : '' }}>Llamada telefónica</option>
                                    <option value="visita" {{ old('action_type') == 'visita' ? 'selected' : '' }}>Visita presencial</option>
                                    <option value="mensaje" {{ old('action_type') == 'mensaje' ? 'selected' : '' }}>Mensaje de texto</option>
                                    <option value="acuerdo" {{ old('action_type') == 'acuerdo' ? 'selected' : '' }}>Acuerdo de pago</option>
                                    <option value="otro" {{ old('action_type') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('action_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes">Notas / Descripción <span class="text-danger">*</span></label>
                                <textarea name="notes" id="notes" rows="4" class="form-control @error('notes') is-invalid @enderror" required>{{ old('notes', 'Gestión de cobranza para el crédito seleccionado.') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="agreement_date">Fecha de Acuerdo (opcional)</label>
                                <input type="date" name="agreement_date" id="agreement_date" class="form-control @error('agreement_date') is-invalid @enderror" value="{{ old('agreement_date', date('Y-m-d')) }}">
                                @error('agreement_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="agreement_amount">Monto Acordado (opcional)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="agreement_amount" id="agreement_amount" class="form-control @error('agreement_amount') is-invalid @enderror" value="{{ old('agreement_amount', '0.00') }}">
                                </div>
                                @error('agreement_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Actualizar Acción
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 