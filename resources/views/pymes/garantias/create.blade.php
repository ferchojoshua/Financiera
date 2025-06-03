@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Registrar Nueva Garantía</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pymes.garantias.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">Cliente</label>
                            <div class="col-md-6">
                                <select id="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->business_name }} ({{ $cliente->tax_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="credit_id" class="col-md-4 col-form-label text-md-right">Crédito Asociado</label>
                            <div class="col-md-6">
                                <select id="credit_id" class="form-control @error('credit_id') is-invalid @enderror" name="credit_id">
                                    <option value="">Sin crédito asignado</option>
                                    @foreach($creditos as $credito)
                                        <option value="{{ $credito->id }}" {{ old('credit_id') == $credito->id ? 'selected' : '' }}>
                                            #{{ $credito->id }} - {{ $credito->amount }}€
                                        </option>
                                    @endforeach
                                </select>
                                @error('credit_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="type" class="col-md-4 col-form-label text-md-right">Tipo de Garantía</label>
                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="real_estate" {{ old('type') == 'real_estate' ? 'selected' : '' }}>Inmueble</option>
                                    <option value="vehicle" {{ old('type') == 'vehicle' ? 'selected' : '' }}>Vehículo</option>
                                    <option value="machinery" {{ old('type') == 'machinery' ? 'selected' : '' }}>Maquinaria</option>
                                    <option value="deposit" {{ old('type') == 'deposit' ? 'selected' : '' }}>Depósito</option>
                                    <option value="inventory" {{ old('type') == 'inventory' ? 'selected' : '' }}>Inventario</option>
                                    <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal (Aval)</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="4" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="value" class="col-md-4 col-form-label text-md-right">Valor Estimado (€)</label>
                            <div class="col-md-6">
                                <input id="value" type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value') }}" required>
                                @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Estado</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente de verificación</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activa</option>
                                    <option value="executed" {{ old('status') == 'executed' ? 'selected' : '' }}>Ejecutada</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="document" class="col-md-4 col-form-label text-md-right">Documento de Respaldo</label>
                            <div class="col-md-6">
                                <input id="document" type="file" class="form-control @error('document') is-invalid @enderror" name="document">
                                <small class="form-text text-muted">
                                    Documentación que respalde la garantía (escritura, título, etc.)
                                </small>
                                @error('document')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">Notas Adicionales</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Registrar Garantía
                                </button>
                                <a href="{{ route('pymes.garantias') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 