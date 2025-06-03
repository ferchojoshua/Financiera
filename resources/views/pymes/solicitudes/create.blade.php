@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Crear Solicitud de Crédito</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pymes.solicitudes.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="client_id" class="col-md-4 col-form-label text-md-right">Cliente</label>
                            <div class="col-md-6">
                                <select id="client_id" class="form-control @error('client_id') is-invalid @enderror" name="client_id" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('client_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->business_name }} ({{ $cliente->tax_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="amount" class="col-md-4 col-form-label text-md-right">Monto Solicitado</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <input id="amount" type="number" step="0.01" min="1000" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
                                </div>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="term" class="col-md-4 col-form-label text-md-right">Plazo (meses)</label>
                            <div class="col-md-6">
                                <input id="term" type="number" min="1" max="60" class="form-control @error('term') is-invalid @enderror" name="term" value="{{ old('term') }}" required>
                                @error('term')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="purpose" class="col-md-4 col-form-label text-md-right">Propósito del Crédito</label>
                            <div class="col-md-6">
                                <select id="purpose" class="form-control @error('purpose') is-invalid @enderror" name="purpose" required>
                                    <option value="">Seleccionar propósito...</option>
                                    <option value="capital_trabajo" {{ old('purpose') == 'capital_trabajo' ? 'selected' : '' }}>Capital de Trabajo</option>
                                    <option value="expansion" {{ old('purpose') == 'expansion' ? 'selected' : '' }}>Expansión de Negocio</option>
                                    <option value="equipamiento" {{ old('purpose') == 'equipamiento' ? 'selected' : '' }}>Compra de Equipos</option>
                                    <option value="inventario" {{ old('purpose') == 'inventario' ? 'selected' : '' }}>Incremento de Inventario</option>
                                    <option value="refinanciamiento" {{ old('purpose') == 'refinanciamiento' ? 'selected' : '' }}>Refinanciamiento</option>
                                    <option value="otro" {{ old('purpose') == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('purpose')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3" id="purpose_detail_container" style="{{ old('purpose') == 'otro' ? '' : 'display: none;' }}">
                            <label for="purpose_detail" class="col-md-4 col-form-label text-md-right">Detallar Propósito</label>
                            <div class="col-md-6">
                                <textarea id="purpose_detail" class="form-control @error('purpose_detail') is-invalid @enderror" name="purpose_detail">{{ old('purpose_detail') }}</textarea>
                                @error('purpose_detail')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción del Proyecto</label>
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
                            <label for="documents" class="col-md-4 col-form-label text-md-right">Documentos de Soporte</label>
                            <div class="col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple>
                                    <label class="custom-file-label" for="documents">Seleccionar archivos...</label>
                                </div>
                                <small class="form-text text-muted">
                                    Puede subir múltiples documentos (estados financieros, facturas, presupuestos, etc.)
                                </small>
                                @error('documents')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Enviar Solicitud
                                </button>
                                <a href="{{ route('pymes.solicitudes') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const purposeSelect = document.getElementById('purpose');
        const purposeDetailContainer = document.getElementById('purpose_detail_container');
        
        purposeSelect.addEventListener('change', function() {
            if (this.value === 'otro') {
                purposeDetailContainer.style.display = '';
            } else {
                purposeDetailContainer.style.display = 'none';
            }
        });
        
        // Para los archivos subidos
        document.getElementById('documents').addEventListener('change', function(e) {
            const fileName = Array.from(e.target.files)
                .map(file => file.name)
                .join(', ');
            
            const label = e.target.nextElementSibling;
            label.innerText = fileName || 'Seleccionar archivos...';
        });
    });
</script>
@endpush
@endsection 