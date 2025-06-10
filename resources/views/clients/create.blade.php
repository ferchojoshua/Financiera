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

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Dirección Principal</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Mapa y Coordenadas -->
                        <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                        <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                        <div class="form-group mt-3">
                            <label>Ubicación en el Mapa</label>
                            <div id="map" style="height: 300px; width: 100%; border-radius: 5px; border: 1px solid #ced4da;"></div>
                            <small class="form-text text-muted">Arrastra el marcador para ajustar la ubicación exacta.</small>
                        </div>

                        <hr>

                        <h5>Información Personal</h5>
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
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap&libraries=places&v=weekly" async defer></script>
<script>
    let map;
    let marker;

    function initMap() {
        const defaultLocation = { lat: 13.6929, lng: -89.2182 }; // Coordenadas de San Salvador por defecto

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 12,
        });

        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
            title: "Ubicación del cliente"
        });

        // Actualizar campos cuando el marcador se mueve
        marker.addListener('dragend', function(event) {
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('lng').value = event.latLng.lng();
        });

        // Sincronizar el mapa con la dirección (opcional pero recomendado)
        const addressInput = document.getElementById('address');
        const autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry && place.geometry.location) {
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                document.getElementById('lat').value = place.geometry.location.lat();
                document.getElementById('lng').value = place.geometry.location.lng();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- Lógica de Estado Civil y Cónyuge ---
        const civilStatusSelect = document.getElementById('civil_status');
        const spouseInfoDiv = document.getElementById('spouse_info');

        function toggleSpouseInfo() {
            const selectedStatus = civilStatusSelect.value;
            if (selectedStatus === 'casado' || selectedStatus === 'union_libre') {
                spouseInfoDiv.style.display = 'block';
            } else {
                spouseInfoDiv.style.display = 'none';
            }
        }

        civilStatusSelect.addEventListener('change', toggleSpouseInfo);
        toggleSpouseInfo(); // Ejecutar al cargar la página por si hay valores antiguos

        // --- Lógica de Cálculo de Negocio ---
        const salesGoodInput = document.getElementById('sales_good');
        const salesBadInput = document.getElementById('sales_bad');
        const weeklyAverageInput = document.getElementById('weekly_average');
        const netProfitInput = document.getElementById('net_profit');

        function calculateBusinessMetrics() {
            const good = parseFloat(salesGoodInput.value) || 0;
            const bad = parseFloat(salesBadInput.value) || 0;

            // Asumimos 6 días de trabajo a la semana para el promedio
            const average = ((good + bad) / 2) * 6;
            weeklyAverageInput.value = average.toFixed(2);

            // Asumimos una ganancia neta del 35% del promedio semanal.
            // Este valor puede ser ajustado según la lógica de negocio.
            const profit = average * 0.35;
            netProfitInput.value = profit.toFixed(2);
        }

        salesGoodInput.addEventListener('input', calculateBusinessMetrics);
        salesBadInput.addEventListener('input', calculateBusinessMetrics);
        calculateBusinessMetrics();

        // --- Lógica de Tipo de Crédito y Validación de Monto ---
        const creditTypeSelect = document.getElementById('credit_type_id');
        const loanAmountInput = document.getElementById('loan_amount');
        const minAmountSpan = document.getElementById('min_amount');
        const maxAmountSpan = document.getElementById('max_amount');
        const interestRateInfo = document.getElementById('interest_rate_info');
        const loanAmountErrorDiv = document.getElementById('loan_amount_error');

        function updateCreditInfo() {
            const selectedOption = creditTypeSelect.options[creditTypeSelect.selectedIndex];
            const min = parseFloat(selectedOption.getAttribute('data-min')) || 0;
            const max = parseFloat(selectedOption.getAttribute('data-max')) || 0;
            const rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;

            minAmountSpan.textContent = min.toLocaleString('es-CO');
            maxAmountSpan.textContent = max.toLocaleString('es-CO');

            if (rate > 0) {
                interestRateInfo.textContent = `Tasa de interés: ${rate}%`;
            } else {
                interestRateInfo.textContent = '';
            }
            
            validateLoanAmount();
        }

        function validateLoanAmount() {
            const selectedOption = creditTypeSelect.options[creditTypeSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                loanAmountInput.classList.remove('is-invalid');
                loanAmountErrorDiv.textContent = '';
                return;
            }

            const min = parseFloat(selectedOption.getAttribute('data-min')) || 0;
            const max = parseFloat(selectedOption.getAttribute('data-max')) || 0;
            const amount = parseFloat(loanAmountInput.value);

            if (amount < min || amount > max) {
                loanAmountInput.classList.add('is-invalid');
                loanAmountErrorDiv.textContent = `El monto debe estar entre $${min.toLocaleString('es-CO')} y $${max.toLocaleString('es-CO')}.`;
            } else {
                loanAmountInput.classList.remove('is-invalid');
                loanAmountErrorDiv.textContent = '';
            }
        }

        creditTypeSelect.addEventListener('change', updateCreditInfo);
        loanAmountInput.addEventListener('input', validateLoanAmount);
        updateCreditInfo(); // Ejecutar al cargar la página
    });
</script>
@endpush