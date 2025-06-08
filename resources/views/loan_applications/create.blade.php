@extends('layouts.app')

@section('title', 'Nueva Solicitud de Crédito')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nueva Solicitud de Crédito</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('loan-applications.index') }}">Solicitudes</a></li>
                <li class="breadcrumb-item active">Nueva Solicitud</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Solicitud</h3>
        </div>
        
        <form action="{{ route('loan-applications.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <!-- Información del Cliente -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client_id">Cliente *</label>
                            <select class="form-control select2 @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">Seleccionar Cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" 
                                            {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} {{ $client->last_name }} - {{ $client->nit }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="credit_type_id">Tipo de Crédito *</label>
                            <select class="form-control @error('credit_type_id') is-invalid @enderror" 
                                    id="credit_type_id" name="credit_type_id" required>
                                <option value="">Seleccionar Tipo...</option>
                                @foreach($creditTypes as $type)
                                    <option value="{{ $type->id }}" 
                                            data-min="{{ $type->min_amount }}"
                                            data-max="{{ $type->max_amount }}"
                                            data-rate="{{ $type->interest_rate }}"
                                            {{ old('credit_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('credit_type_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Información del Crédito -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount_requested">Monto Solicitado *</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" 
                                       class="form-control @error('amount_requested') is-invalid @enderror" 
                                       id="amount_requested" name="amount_requested" 
                                       value="{{ old('amount_requested') }}" required>
                            </div>
                            <small class="form-text text-muted">
                                Monto mínimo: $<span id="min_amount">0</span> - 
                                Monto máximo: $<span id="max_amount">0</span>
                            </small>
                            @error('amount_requested')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="term_months">Plazo (meses) *</label>
                            <input type="number" class="form-control @error('term_months') is-invalid @enderror" 
                                   id="term_months" name="term_months" 
                                   value="{{ old('term_months') }}" required>
                            @error('term_months')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="payment_frequency">Frecuencia de Pago *</label>
                            <select class="form-control @error('payment_frequency') is-invalid @enderror" 
                                    id="payment_frequency" name="payment_frequency" required>
                                <option value="">Seleccionar Frecuencia...</option>
                                <option value="daily" {{ old('payment_frequency') == 'daily' ? 'selected' : '' }}>Diario</option>
                                <option value="weekly" {{ old('payment_frequency') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                <option value="biweekly" {{ old('payment_frequency') == 'biweekly' ? 'selected' : '' }}>Quincenal</option>
                                <option value="monthly" {{ old('payment_frequency') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                            </select>
                            @error('payment_frequency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="col-12">
                        <div class="form-group">
                            <label for="notes">Notas/Observaciones</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Solicitud
                </button>
                <a href="{{ route('loan-applications.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2 para el selector de cliente
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccionar Cliente...',
        allowClear: true
    });

    // Actualizar límites de monto cuando cambia el tipo de crédito
    $('#credit_type_id').change(function() {
        const selected = $(this).find('option:selected');
        $('#min_amount').text(selected.data('min') || 0);
        $('#max_amount').text(selected.data('max') || 0);
        
        // Actualizar los atributos min y max del campo monto
        $('#amount_requested').attr({
            'min': selected.data('min') || 0,
            'max': selected.data('max') || 999999999
        });
    });

    // Validar monto cuando cambia
    $('#amount_requested').on('input', function() {
        const value = parseFloat($(this).val());
        const min = parseFloat($('#min_amount').text());
        const max = parseFloat($('#max_amount').text());

        if (value < min) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text(`El monto mínimo es $${min}`);
        } else if (value > max) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text(`El monto máximo es $${max}`);
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endpush 