@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Cliente: {{ $client->full_name }}</h3>
                </div>
                
                <form action="{{ route('clients.update', $client) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <!-- Información Personal -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h4>Información Personal</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Nombre *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $client->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name">Apellido *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $client->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $client->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nit">NIT *</label>
                                    <input type="text" class="form-control @error('nit') is-invalid @enderror" 
                                           id="nit" name="nit" value="{{ old('nit', $client->nit) }}" required>
                                    @error('nit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dui">DUI</label>
                                    <input type="text" class="form-control @error('dui') is-invalid @enderror" 
                                           id="dui" name="dui" value="{{ old('dui', $client->dui) }}">
                                    @error('dui')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone">Teléfono *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $client->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="birthdate">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                           id="birthdate" name="birthdate" 
                                           value="{{ old('birthdate', $client->birthdate ? $client->birthdate->format('Y-m-d') : '') }}">
                                    @error('birthdate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h4>Dirección</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Dirección Principal</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $client->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Mapa y Coordenadas -->
                        <input type="hidden" name="lat" id="lat" value="{{ old('lat', $client->lat) }}">
                        <input type="hidden" name="lng" id="lng" value="{{ old('lng', $client->lng) }}">
                        <div class="form-group mt-3">
                            <label>Ubicación en el Mapa</label>
                            <div id="map" style="height: 300px; width: 100%; border-radius: 5px; border: 1px solid #ced4da;"></div>
                            <small class="form-text text-muted">Arrastra el marcador para ajustar la ubicación exacta.</small>
                        </div>

                        <hr>

                        <h5>Información Personal</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">Ciudad</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $client->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">Departamento</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           id="state" name="state" value="{{ old('state', $client->state) }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="province">Provincia/Municipio</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                           id="province" name="province" value="{{ old('province', $client->province) }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h4>Información Adicional</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <select class="form-control @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender">
                                        <option value="">Seleccionar...</option>
                                        <option value="M" {{ old('gender', $client->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                        <option value="F" {{ old('gender', $client->gender) == 'F' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="civil_status">Estado Civil</label>
                                    <select class="form-control @error('civil_status') is-invalid @enderror" 
                                            id="civil_status" name="civil_status">
                                        <option value="">Seleccionar...</option>
                                        <option value="soltero" {{ old('civil_status', $client->civil_status) == 'soltero' ? 'selected' : '' }}>Soltero/a</option>
                                        <option value="casado" {{ old('civil_status', $client->civil_status) == 'casado' ? 'selected' : '' }}>Casado/a</option>
                                        <option value="divorciado" {{ old('civil_status', $client->civil_status) == 'divorciado' ? 'selected' : '' }}>Divorciado/a</option>
                                        <option value="viudo" {{ old('civil_status', $client->civil_status) == 'viudo' ? 'selected' : '' }}>Viudo/a</option>
                                        <option value="union_libre" {{ old('civil_status', $client->civil_status) == 'union_libre' ? 'selected' : '' }}>Unión Libre</option>
                                    </select>
                                    @error('civil_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="house_type">Tipo de Vivienda</label>
                                    <select class="form-control @error('house_type') is-invalid @enderror" 
                                            id="house_type" name="house_type">
                                        <option value="">Seleccionar...</option>
                                        <option value="propia" {{ old('house_type', $client->house_type) == 'propia' ? 'selected' : '' }}>Propia</option>
                                        <option value="alquilada" {{ old('house_type', $client->house_type) == 'alquilada' ? 'selected' : '' }}>Alquilada</option>
                                        <option value="familiar" {{ old('house_type', $client->house_type) == 'familiar' ? 'selected' : '' }}>Familiar</option>
                                    </select>
                                    @error('house_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="client_type_id">Tipo de Cliente</label>
                                    <select class="form-control @error('client_type_id') is-invalid @enderror" 
                                            id="client_type_id" name="client_type_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($clientTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('client_type_id', $client->client_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información del Cónyuge (condicional) -->
                        <div class="row mt-3" id="spouse_info" style="display: none;">
                            <div class="col-md-12 mb-3">
                                <h4>Información del Cónyuge</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="spouse_name">Nombre del Cónyuge</label>
                                    <input type="text" class="form-control @error('spouse_name') is-invalid @enderror" 
                                           id="spouse_name" name="spouse_name" value="{{ old('spouse_name', $client->spouse_name) }}">
                                    @error('spouse_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="spouse_job">Ocupación del Cónyuge</label>
                                    <input type="text" class="form-control @error('spouse_job') is-invalid @enderror" 
                                           id="spouse_job" name="spouse_job" value="{{ old('spouse_job', $client->spouse_job) }}">
                                    @error('spouse_job')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="spouse_phone">Teléfono del Cónyuge</label>
                                    <input type="text" class="form-control @error('spouse_phone') is-invalid @enderror" 
                                           id="spouse_phone" name="spouse_phone" value="{{ old('spouse_phone', $client->spouse_phone) }}">
                                    @error('spouse_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de Negocio -->
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h4>Información de Negocio</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_name">Nombre del Negocio</label>
                                    <input type="text" class="form-control @error('business_name') is-invalid @enderror" 
                                           id="business_name" name="business_name" value="{{ old('business_name', $client->business_name) }}">
                                    @error('business_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_sector">Sector del Negocio</label>
                                    <input type="text" class="form-control @error('business_sector') is-invalid @enderror" 
                                           id="business_sector" name="business_sector" value="{{ old('business_sector', $client->business_sector) }}">
                                    @error('business_sector')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="economic_activity">Actividad Económica</label>
                                    <textarea class="form-control @error('economic_activity') is-invalid @enderror" 
                                              id="economic_activity" name="economic_activity" rows="2">{{ old('economic_activity', $client->economic_activity) }}</textarea>
                                    @error('economic_activity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_good">Ventas Buenas</label>
                                    <input type="number" step="0.01" class="form-control @error('sales_good') is-invalid @enderror" 
                                           id="sales_good" name="sales_good" value="{{ old('sales_good', $client->sales_good) }}">
                                    @error('sales_good')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_bad">Ventas Malas</label>
                                    <input type="number" step="0.01" class="form-control @error('sales_bad') is-invalid @enderror" 
                                           id="sales_bad" name="sales_bad" value="{{ old('sales_bad', $client->sales_bad) }}">
                                    @error('sales_bad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weekly_average">Promedio Semanal</label>
                                    <input type="number" step="0.01" class="form-control @error('weekly_average') is-invalid @enderror" 
                                           id="weekly_average" name="weekly_average" value="{{ old('weekly_average', $client->weekly_average) }}">
                                    @error('weekly_average')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="net_profit">Utilidad Neta</label>
                                    <input type="number" step="0.01" class="form-control @error('net_profit') is-invalid @enderror" 
                                           id="net_profit" name="net_profit" value="{{ old('net_profit', $client->net_profit) }}">
                                    @error('net_profit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Estado y Notas -->
                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <h4>Estado y Notas</h4>
                                <hr>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active">Estado del Cliente</label>
                                    <select class="form-control @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $client->is_active) ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('is_active', $client->is_active) ? '' : 'selected' }}>Inactivo</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="blacklisted">Lista Negra</label>
                                    <select class="form-control @error('blacklisted') is-invalid @enderror" 
                                            id="blacklisted" name="blacklisted">
                                        <option value="0" {{ old('blacklisted', $client->blacklisted) ? '' : 'selected' }}>No</option>
                                        <option value="1" {{ old('blacklisted', $client->blacklisted) ? 'selected' : '' }}>Sí</option>
                                    </select>
                                    @error('blacklisted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="assigned_agent_id">Agente Asignado</label>
                                    <select class="form-control @error('assigned_agent_id') is-invalid @enderror" 
                                            id="assigned_agent_id" name="assigned_agent_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" 
                                                    {{ old('assigned_agent_id', $client->assigned_agent_id) == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-12" id="blacklist_reason_container" style="display: none;">
                                <div class="form-group">
                                    <label for="blacklist_reason">Razón de Lista Negra</label>
                                    <textarea class="form-control @error('blacklist_reason') is-invalid @enderror" 
                                              id="blacklist_reason" name="blacklist_reason" rows="2">{{ old('blacklist_reason', $client->blacklist_reason) }}</textarea>
                                    @error('blacklist_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="credit_notes">Notas de Crédito</label>
                                    <textarea class="form-control @error('credit_notes') is-invalid @enderror" 
                                              id="credit_notes" name="credit_notes" rows="3">{{ old('credit_notes', $client->credit_notes) }}</textarea>
                                    @error('credit_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
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
document.addEventListener('DOMContentLoaded', function () {
    // Tu script existente para el formulario de edición...

    // --- INICIO SCRIPT DE MAPA ---
    let map;
    let marker;

    function initMap() {
        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');
        const defaultLocation = { lat: 13.6929, lng: -89.2182 }; // San Salvador por defecto

        let initialLat = parseFloat(latInput.value);
        let initialLng = parseFloat(lngInput.value);

        const initialLocation = !isNaN(initialLat) && !isNaN(initialLng) && initialLat != 0 ? { lat: initialLat, lng: initialLng } : defaultLocation;

        map = new google.maps.Map(document.getElementById("map"), {
            center: initialLocation,
            zoom: 15,
        });

        marker = new google.maps.Marker({
            position: initialLocation,
            map: map,
            draggable: true,
            title: "Ubicación del cliente"
        });

        marker.addListener('dragend', function(event) {
            latInput.value = event.latLng.lat();
            lngInput.value = event.latLng.lng();
        });

        const addressInput = document.getElementById('address');
        const autocomplete = new google.maps.places.Autocomplete(addressInput);
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry && place.geometry.location) {
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                latInput.value = place.geometry.location.lat();
                lngInput.value = place.geometry.location.lng();
            }
        });
    }

    // Asegurarse de que initMap esté disponible globalmente para el callback de Google Maps
    window.initMap = initMap;
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar información del cónyuge según estado civil
    const civilStatusSelect = document.getElementById('civil_status');
    const spouseInfo = document.getElementById('spouse_info');
    
    function toggleSpouseInfo() {
        if (civilStatusSelect.value === 'casado') {
            spouseInfo.style.display = 'flex';
            document.getElementById('spouse_name').required = true;
        } else {
            spouseInfo.style.display = 'none';
            document.getElementById('spouse_name').required = false;
        }
    }
    
    civilStatusSelect.addEventListener('change', toggleSpouseInfo);
    toggleSpouseInfo(); // Ejecutar al cargar la página
    
    // Mostrar/ocultar razón de lista negra
    const blacklistedSelect = document.getElementById('blacklisted');
    const blacklistReasonContainer = document.getElementById('blacklist_reason_container');
    const blacklistReasonInput = document.getElementById('blacklist_reason');
    
    function toggleBlacklistReason() {
        if (blacklistedSelect.value === '1') {
            blacklistReasonContainer.style.display = 'block';
            blacklistReasonInput.required = true;
        } else {
            blacklistReasonContainer.style.display = 'none';
            blacklistReasonInput.required = false;
        }
    }
    
    blacklistedSelect.addEventListener('change', toggleBlacklistReason);
    toggleBlacklistReason(); // Ejecutar al cargar la página
    
    // Validación del formulario
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
@endpush 