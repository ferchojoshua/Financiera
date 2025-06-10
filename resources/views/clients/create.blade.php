@extends('layouts.app')

@section('title', 'Registro Rápido de Cliente y Solicitud')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registro Rápido de Cliente y Solicitud</h3>
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger mx-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('clients.store') }}" id="clientForm">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Información Personal -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h4>Información Personal</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="last_name" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="nit" class="form-label">Cédula/NIT *</label>
                                    <input type="text" class="form-control @error('nit') is-invalid @enderror" 
                                           id="nit" name="nit" value="{{ old('nit') }}" required>
                                    @error('nit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Teléfono *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="route_id" class="form-label">Ruta</label>
                                    <select class="form-select @error('route_id') is-invalid @enderror" 
                                            id="route_id" name="route_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($routes ?? [] as $route)
                                            <option value="{{ $route->id }}" 
                                                    {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                                {{ $route->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('route_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="address" class="form-label">Dirección *</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Personal Adicional -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="gender" class="form-label">Género</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender">
                                        <option value="">Seleccionar...</option>
                                        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="civil_status" class="form-label">Estado Civil</label>
                                    <select class="form-select @error('civil_status') is-invalid @enderror" 
                                            id="civil_status" name="civil_status">
                                        <option value="">Seleccionar...</option>
                                        <option value="soltero" {{ old('civil_status') == 'soltero' ? 'selected' : '' }}>Soltero/a</option>
                                        <option value="casado" {{ old('civil_status') == 'casado' ? 'selected' : '' }}>Casado/a</option>
                                        <option value="divorciado" {{ old('civil_status') == 'divorciado' ? 'selected' : '' }}>Divorciado/a</option>
                                        <option value="viudo" {{ old('civil_status') == 'viudo' ? 'selected' : '' }}>Viudo/a</option>
                                        <option value="union_libre" {{ old('civil_status') == 'union_libre' ? 'selected' : '' }}>Unión Libre</option>
                                    </select>
                                    @error('civil_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="house_type" class="form-label">Tipo de Vivienda</label>
                                    <select class="form-select @error('house_type') is-invalid @enderror" 
                                            id="house_type" name="house_type">
                                        <option value="">Seleccionar...</option>
                                        <option value="propia" {{ old('house_type') == 'propia' ? 'selected' : '' }}>Propia</option>
                                        <option value="alquilada" {{ old('house_type') == 'alquilada' ? 'selected' : '' }}>Alquilada</option>
                                        <option value="familiar" {{ old('house_type') == 'familiar' ? 'selected' : '' }}>Familiar</option>
                                    </select>
                                    @error('house_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información del Cónyuge -->
                        <div id="spouse_info" style="display: none;">
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <h4>Información del Cónyuge</h4>
                                    <hr>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="spouse_name" class="form-label">Nombre del Cónyuge</label>
                                        <input type="text" class="form-control @error('spouse_name') is-invalid @enderror" 
                                               id="spouse_name" name="spouse_name" value="{{ old('spouse_name') }}">
                                        @error('spouse_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="spouse_job" class="form-label">Ocupación del Cónyuge</label>
                                        <input type="text" class="form-control @error('spouse_job') is-invalid @enderror" 
                                               id="spouse_job" name="spouse_job" value="{{ old('spouse_job') }}">
                                        @error('spouse_job')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="spouse_phone" class="form-label">Teléfono del Cónyuge</label>
                                        <input type="text" class="form-control @error('spouse_phone') is-invalid @enderror" 
                                               id="spouse_phone" name="spouse_phone" value="{{ old('spouse_phone') }}">
                                        @error('spouse_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Negocio -->
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h4>Información del Negocio</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="business_type" class="form-label">Tipo de Negocio *</label>
                                    <input type="text" class="form-control @error('business_type') is-invalid @enderror" 
                                           id="business_type" name="business_type" value="{{ old('business_type') }}" required>
                                    @error('business_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="business_time" class="form-label">Tiempo del Negocio (años) *</label>
                                    <input type="number" class="form-control @error('business_time') is-invalid @enderror" 
                                           id="business_time" name="business_time" value="{{ old('business_time') }}" required>
                                    @error('business_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="business_location" class="form-label">Local</label>
                                    <select class="form-select @error('business_location') is-invalid @enderror" 
                                            id="business_location" name="business_location">
                                        <option value="">Seleccionar...</option>
                                        <option value="propio" {{ old('business_location') == 'propio' ? 'selected' : '' }}>Propio</option>
                                        <option value="alquilado" {{ old('business_location') == 'alquilado' ? 'selected' : '' }}>Alquilado</option>
                                        <option value="ambulante" {{ old('business_location') == 'ambulante' ? 'selected' : '' }}>Ambulante</option>
                                    </select>
                                    @error('business_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="sales_good" class="form-label">Ventas Días Buenos *</label>
                                    <input type="number" step="0.01" class="form-control @error('sales_good') is-invalid @enderror" 
                                           id="sales_good" name="sales_good" value="{{ old('sales_good') }}" required>
                                    @error('sales_good')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="sales_bad" class="form-label">Ventas Días Malos *</label>
                                    <input type="number" step="0.01" class="form-control @error('sales_bad') is-invalid @enderror" 
                                           id="sales_bad" name="sales_bad" value="{{ old('sales_bad') }}" required>
                                    @error('sales_bad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="weekly_average" class="form-label">Promedio Semanal *</label>
                                    <input type="number" step="0.01" class="form-control @error('weekly_average') is-invalid @enderror" 
                                           id="weekly_average" name="weekly_average" value="{{ old('weekly_average') }}" readonly>
                                    @error('weekly_average')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="net_profit" class="form-label">Ganancias Netas *</label>
                                    <input type="number" step="0.01" class="form-control @error('net_profit') is-invalid @enderror" 
                                           id="net_profit" name="net_profit" value="{{ old('net_profit') }}" readonly>
                                    @error('net_profit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Solicitud de Préstamo -->
                        <div class="row mt-4">
                            <div class="col-md-12 mb-3">
                                <h4>Solicitud de Préstamo</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="credit_type_id" class="form-label">Tipo de Crédito *</label>
                                    <select class="form-select @error('credit_type_id') is-invalid @enderror" 
                                            id="credit_type_id" name="credit_type_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($creditTypes ?? [] as $type)
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
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Monto mínimo: $<span id="min_amount">0</span> - 
                                        Monto máximo: $<span id="max_amount">0</span>
                                    </small>
                                    <small id="interest_rate_info" class="form-text text-info"></small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="loan_amount" class="form-label">Monto Solicitado *</label>
                                    <input type="number" step="0.01" class="form-control @error('loan_amount') is-invalid @enderror" 
                                           id="loan_amount" name="loan_amount" value="{{ old('loan_amount') }}" required>
                                    <div id="loan_amount_error" class="invalid-feedback"></div>
                                    @error('loan_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="payment_frequency" class="form-label">Frecuencia de Pago *</label>
                                    <select class="form-select @error('payment_frequency') is-invalid @enderror" 
                                            id="payment_frequency" name="payment_frequency" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="daily" {{ old('payment_frequency') == 'daily' ? 'selected' : '' }}>Diario</option>
                                        <option value="weekly" {{ old('payment_frequency') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                        <option value="biweekly" {{ old('payment_frequency') == 'biweekly' ? 'selected' : '' }}>Quincenal</option>
                                        <option value="monthly" {{ old('payment_frequency') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                                    </select>
                                    @error('payment_frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">Notas</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="1">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Cliente y Crear Solicitud</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Función para formatear montos en formato de moneda
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(amount);
    }
    
    // Función para actualizar los montos mínimos y máximos
    function updateAmountLimits() {
        const selectedOption = $('#credit_type_id option:selected');
        const minAmount = parseFloat(selectedOption.data('min')) || 0;
        const maxAmount = parseFloat(selectedOption.data('max')) || 0;
        const interestRate = parseFloat(selectedOption.data('rate')) || 0;
        
        // Actualizar los textos informativos
        $('#min_amount').text(formatCurrency(minAmount));
        $('#max_amount').text(formatCurrency(maxAmount));
        
        // Actualizar los atributos del campo de monto
        $('#loan_amount').attr({
            'min': minAmount,
            'max': maxAmount,
            'placeholder': `Entre ${formatCurrency(minAmount)} y ${formatCurrency(maxAmount)}`
        });
        
        // Mostrar tasa de interés si está disponible
        if (interestRate > 0) {
            $('#interest_rate_info').text(`Tasa de interés: ${interestRate}% anual`).show();
        } else {
            $('#interest_rate_info').hide();
        }
        
        // Validar el monto actual si hay uno
        validateAmount();
    }
    
    // Función para validar el monto ingresado
    function validateAmount() {
        const input = $('#loan_amount');
        const amount = parseFloat(input.val());
        const min = parseFloat(input.attr('min'));
        const max = parseFloat(input.attr('max'));
        
        if (amount && (amount < min || amount > max)) {
            input.addClass('is-invalid');
            $('#loan_amount_error').text(
                `El monto debe estar entre ${formatCurrency(min)} y ${formatCurrency(max)}`
            ).show();
        } else {
            input.removeClass('is-invalid');
            $('#loan_amount_error').hide();
        }
    }
    
    // Actualizar cuando cambie el tipo de crédito
    $('#credit_type_id').change(updateAmountLimits);
    
    // Validar cuando cambie el monto
    $('#loan_amount').on('input', validateAmount);
    
    // Actualizar al cargar la página
    updateAmountLimits();
    
    // Mostrar/ocultar información del cónyuge
    $('#civil_status').change(function() {
        if ($(this).val() === 'casado') {
            $('#spouse_info').slideDown();
            $('#spouse_name').prop('required', true);
        } else {
            $('#spouse_info').slideUp();
            $('#spouse_name').prop('required', false);
        }
    }).trigger('change');
    
    // Calcular promedio semanal y ganancias netas
    function calculateAverages() {
        const salesGood = parseFloat($('#sales_good').val()) || 0;
        const salesBad = parseFloat($('#sales_bad').val()) || 0;
        
        // Calcular promedio semanal (asumiendo 6 días buenos y 1 malo por semana)
        const weeklyAverage = (salesGood * 6 + salesBad) / 7;
        $('#weekly_average').val(weeklyAverage.toFixed(2));
        
        // Calcular ganancias netas (asumiendo 30% de margen)
        const netProfit = weeklyAverage * 0.30;
        $('#net_profit').val(netProfit.toFixed(2));
    }
    
    // Calcular cuando cambien las ventas
    $('#sales_good, #sales_bad').on('input', calculateAverages);
    
    // Validar el formulario antes de enviar
    $('#clientForm').on('submit', function(e) {
        const loanAmount = $('#loan_amount');
        if (loanAmount.val()) {
            const amount = parseFloat(loanAmount.val());
            const min = parseFloat(loanAmount.attr('min'));
            const max = parseFloat(loanAmount.attr('max'));
            
            if (amount < min || amount > max) {
                e.preventDefault();
                alert(`El monto del préstamo debe estar entre ${formatCurrency(min)} y ${formatCurrency(max)}`);
                loanAmount.focus();
            }
        }
    });
});
</script>
@endpush