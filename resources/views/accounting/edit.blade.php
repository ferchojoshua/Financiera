@extends('layouts.app')

@section('title', 'Editar Entrada Contable')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Entrada Contable</h3>
                </div>
                
                <form action="{{ route('accounting.update', $entry->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entry_date">Fecha *</label>
                                    <input type="date" class="form-control @error('entry_date') is-invalid @enderror" 
                                           id="entry_date" name="entry_date" 
                                           value="{{ old('entry_date', $entry->entry_date->format('Y-m-d')) }}" required>
                                    @error('entry_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entry_type">Tipo *</label>
                                    <select class="form-control @error('entry_type') is-invalid @enderror" 
                                            id="entry_type" name="entry_type" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="ingreso" {{ old('entry_type', $entry->entry_type) == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                        <option value="gasto" {{ old('entry_type', $entry->entry_type) == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                        <option value="ajuste" {{ old('entry_type', $entry->entry_type) == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Descripción *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="2" required>{{ old('description', $entry->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Monto *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount', $entry->amount) }}" required>
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Categoría *</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                           id="category" name="category" value="{{ old('category', $entry->category) }}" required>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference">Referencia</label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                           id="reference" name="reference" value="{{ old('reference', $entry->reference) }}">
                                    @error('reference')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="accounting_account">Cuenta Contable</label>
                                    <input type="text" class="form-control @error('accounting_account') is-invalid @enderror" 
                                           id="accounting_account" name="accounting_account" 
                                           value="{{ old('accounting_account', $entry->accounting_account) }}">
                                    @error('accounting_account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $entry->notes) }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 