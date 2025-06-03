@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Nuevo Producto Financiero</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pymes.productos.store') }}">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="nombre" class="col-md-4 col-form-label text-md-right">Nombre del Producto</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autofocus>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="descripcion" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="tasa_interes" class="col-md-4 col-form-label text-md-right">Tasa de Interés (%)</label>
                            <div class="col-md-6">
                                <input id="tasa_interes" type="number" step="0.01" min="0" max="100" class="form-control @error('tasa_interes') is-invalid @enderror" name="tasa_interes" value="{{ old('tasa_interes') }}" required>
                                @error('tasa_interes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="plazo_maximo" class="col-md-4 col-form-label text-md-right">Plazo Máximo (meses)</label>
                            <div class="col-md-6">
                                <input id="plazo_maximo" type="number" min="1" max="120" class="form-control @error('plazo_maximo') is-invalid @enderror" name="plazo_maximo" value="{{ old('plazo_maximo') }}" required>
                                @error('plazo_maximo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="monto_minimo" class="col-md-4 col-form-label text-md-right">Monto Mínimo (€)</label>
                            <div class="col-md-6">
                                <input id="monto_minimo" type="number" step="100" min="0" class="form-control @error('monto_minimo') is-invalid @enderror" name="monto_minimo" value="{{ old('monto_minimo') }}" required>
                                @error('monto_minimo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="monto_maximo" class="col-md-4 col-form-label text-md-right">Monto Máximo (€)</label>
                            <div class="col-md-6">
                                <input id="monto_maximo" type="number" step="1000" min="0" class="form-control @error('monto_maximo') is-invalid @enderror" name="monto_maximo" value="{{ old('monto_maximo') }}" required>
                                @error('monto_maximo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="comision_apertura" class="col-md-4 col-form-label text-md-right">Comisión de Apertura (%)</label>
                            <div class="col-md-6">
                                <input id="comision_apertura" type="number" step="0.01" min="0" max="10" class="form-control @error('comision_apertura') is-invalid @enderror" name="comision_apertura" value="{{ old('comision_apertura', 0) }}">
                                @error('comision_apertura')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="requisitos" class="col-md-4 col-form-label text-md-right">Requisitos</label>
                            <div class="col-md-6">
                                <textarea id="requisitos" class="form-control @error('requisitos') is-invalid @enderror" name="requisitos" rows="4">{{ old('requisitos') }}</textarea>
                                @error('requisitos')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="activo" class="col-md-4 col-form-label text-md-right">Estado</label>
                            <div class="col-md-6 mt-2">
                                <div class="form-check">
                                    <input id="activo" type="checkbox" class="form-check-input @error('activo') is-invalid @enderror" name="activo" value="1" {{ old('activo') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo</label>
                                </div>
                                @error('activo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Crear Producto
                                </button>
                                <a href="{{ route('pymes.productos') }}" class="btn btn-secondary">
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