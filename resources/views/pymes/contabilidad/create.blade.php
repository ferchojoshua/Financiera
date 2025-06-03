@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Nueva Entrada Contable</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pymes.contabilidad.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entry_date" class="form-label">Fecha</label>
                                    <input type="date" class="form-control @error('entry_date') is-invalid @enderror" id="entry_date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                                    @error('entry_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entry_type" class="form-label">Tipo de Entrada</label>
                                    <select class="form-control @error('entry_type') is-invalid @enderror" id="entry_type" name="entry_type" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="ingreso" {{ old('entry_type') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                                        <option value="gasto" {{ old('entry_type') == 'gasto' ? 'selected' : '' }}>Gasto</option>
                                        <option value="ajuste" {{ old('entry_type') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                                    </select>
                                    @error('entry_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Monto (€)</label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reference" class="form-label">Referencia</label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" name="reference" value="{{ old('reference') }}">
                                    @error('reference')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoría</label>
                                    <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="intereses" {{ old('category') == 'intereses' ? 'selected' : '' }}>Intereses</option>
                                        <option value="comisiones" {{ old('category') == 'comisiones' ? 'selected' : '' }}>Comisiones</option>
                                        <option value="personal" {{ old('category') == 'personal' ? 'selected' : '' }}>Personal</option>
                                        <option value="operativo" {{ old('category') == 'operativo' ? 'selected' : '' }}>Operativo</option>
                                        <option value="impuestos" {{ old('category') == 'impuestos' ? 'selected' : '' }}>Impuestos</option>
                                        <option value="otros" {{ old('category') == 'otros' ? 'selected' : '' }}>Otros</option>
                                    </select>
                                    @error('category')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subcategory" class="form-label">Subcategoría</label>
                                    <input type="text" class="form-control @error('subcategory') is-invalid @enderror" id="subcategory" name="subcategory" value="{{ old('subcategory') }}">
                                    @error('subcategory')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credit_id" class="form-label">Crédito Asociado</label>
                                    <select class="form-control @error('credit_id') is-invalid @enderror" id="credit_id" name="credit_id">
                                        <option value="">Ninguno</option>
                                        @foreach($creditos ?? [] as $credito)
                                            <option value="{{ $credito->id }}" {{ old('credit_id') == $credito->id ? 'selected' : '' }}>
                                                #{{ $credito->id }} - {{ $credito->user->business_name ?? 'Cliente' }} (€ {{ number_format($credito->amount, 2, ',', '.') }})
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Cliente Asociado</label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                        <option value="">Ninguno</option>
                                        @foreach($clientes ?? [] as $cliente)
                                            <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->business_name ?? $cliente->name }} ({{ $cliente->tax_id ?? $cliente->nif }})
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
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="accounting_account" class="form-label">Cuenta Contable</label>
                                    <input type="text" class="form-control @error('accounting_account') is-invalid @enderror" id="accounting_account" name="accounting_account" value="{{ old('accounting_account') }}">
                                    @error('accounting_account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estado</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="attachment" class="form-label">Adjunto</label>
                            <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                            <small class="form-text text-muted">Facturas, recibos u otros documentos de respaldo (PDF, JPG, PNG).</small>
                            @error('attachment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-center mt-5">
                            <button type="submit" class="btn btn-primary me-3">
                                <i class="fas fa-save"></i> Guardar Entrada
                            </button>
                            <a href="{{ route('pymes.contabilidad') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 