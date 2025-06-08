@extends('layouts.app')

@section('title', 'Registro de Actividades')

@section('styles')
<style>
    #mapa {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .location-info {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
    .btn-location {
        margin-right: 10px;
    }
    .coord-display {
        font-family: monospace;
        font-size: 14px;
    }
    .activity-form {
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-clipboard-list"></i> Registro de Actividades</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Registro de Actividades</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mi Ubicación Actual</h3>
                </div>
                <div class="card-body">
                    <div id="mapa"></div>
                    
                    <div class="location-info">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-map-marker-alt"></i> Información de Ubicación</h5>
                                <p>
                                    <strong>Latitud:</strong> 
                                    <span class="coord-display" id="lat-display">--.------</span>
                                </p>
                                <p>
                                    <strong>Longitud:</strong> 
                                    <span class="coord-display" id="lng-display">--.------</span>
                                </p>
                                <p>
                                    <strong>Dirección:</strong> 
                                    <span id="address-display">No disponible</span>
                                </p>
                                <p>
                                    <strong>Precisión:</strong> 
                                    <span id="accuracy-display">No disponible</span>
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary btn-location" id="obtenerUbicacion">
                                    <i class="fas fa-crosshairs"></i> Obtener Mi Ubicación
                                </button>
                                <button type="button" class="btn btn-success" id="guardarUbicacion" disabled>
                                    <i class="fas fa-save"></i> Guardar Ubicación
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="activity-form">
                        <h5><i class="fas fa-tasks"></i> Registro de Actividad</h5>
                        <form id="activityForm">
                            <div class="form-group">
                                <label for="tipoActividad">Tipo de Actividad</label>
                                <select class="form-control" id="tipoActividad" required>
                                    <option value="">Seleccione...</option>
                                    <option value="visita">Visita a Cliente</option>
                                    <option value="cobro">Cobro</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="otra">Otra</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="cliente">Cliente</label>
                                <select class="form-control" id="cliente" required>
                                    <option value="">Seleccione...</option>
                                    <!-- Los clientes se cargarán por AJAX -->
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="resultado">Resultado</label>
                                <select class="form-control" id="resultado" required>
                                    <option value="">Seleccione...</option>
                                    <option value="exitoso">Exitoso</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="no_exitoso">No Exitoso</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="incluirUbicacion" checked>
                                    <label class="custom-control-label" for="incluirUbicacion">Incluir mi ubicación actual</label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Registrar Actividad
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
<script>
    let map;
    let marker;
    let currentPosition = null;
    let geocoder;
    
    // Inicializar el mapa
    function initMap() {
        // Centrar en México por defecto
        const defaultPosition = {lat: 23.6345, lng: -102.5528};
        
        map = new google.maps.Map(document.getElementById('mapa'), {
            center: defaultPosition,
            zoom: 5
        });
        
        geocoder = new google.maps.Geocoder();
        
        // Si hay permisos, intentar obtener la ubicación al cargar
        if (navigator.geolocation) {
            document.getElementById('obtenerUbicacion').addEventListener('click', obtenerUbicacionActual);
        } else {
            alert('Tu navegador no soporta geolocalización');
            document.getElementById('obtenerUbicacion').disabled = true;
        }
        
        // Configurar evento de guardado
        document.getElementById('guardarUbicacion').addEventListener('click', guardarUbicacion);
        
        // Cargar lista de clientes
        cargarClientes();
        
        // Configurar formulario de actividades
        document.getElementById('activityForm').addEventListener('submit', registrarActividad);
    }
    
    // Obtener ubicación actual
    function obtenerUbicacionActual() {
        if (navigator.geolocation) {
            document.getElementById('obtenerUbicacion').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localizando...';
            document.getElementById('obtenerUbicacion').disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    // Guardar posición actual
                    currentPosition = {
                        lat: pos.lat,
                        lng: pos.lng,
                        accuracy: position.coords.accuracy
                    };
                    
                    // Actualizar la interfaz
                    document.getElementById('lat-display').textContent = pos.lat.toFixed(6);
                    document.getElementById('lng-display').textContent = pos.lng.toFixed(6);
                    document.getElementById('accuracy-display').textContent = 
                        position.coords.accuracy > 1000 
                            ? `${(position.coords.accuracy/1000).toFixed(2)} km`
                            : `${position.coords.accuracy.toFixed(0)} m`;
                    
                    // Centrar mapa
                    map.setCenter(pos);
                    map.setZoom(15);
                    
                    // Agregar marcador
                    if (marker) {
                        marker.setPosition(pos);
                    } else {
                        marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            animation: google.maps.Animation.DROP,
                            title: 'Mi ubicación'
                        });
                    }
                    
                    // Obtener dirección
                    geocoder.geocode({'location': pos}, function(results, status) {
                        if (status === 'OK') {
                            if (results[0]) {
                                document.getElementById('address-display').textContent = results[0].formatted_address;
                                currentPosition.direccion = results[0].formatted_address;
                            }
                        }
                    });
                    
                    // Habilitar botón de guardar
                    document.getElementById('guardarUbicacion').disabled = false;
                    document.getElementById('obtenerUbicacion').innerHTML = '<i class="fas fa-crosshairs"></i> Actualizar Ubicación';
                    document.getElementById('obtenerUbicacion').disabled = false;
                },
                (error) => {
                    let errorMessage = 'Error desconocido al obtener la ubicación';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Usuario denegó la solicitud de geolocalización';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'La información de ubicación no está disponible';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'La solicitud para obtener la ubicación del usuario expiró';
                            break;
                    }
                    
                    alert(errorMessage);
                    document.getElementById('obtenerUbicacion').innerHTML = '<i class="fas fa-crosshairs"></i> Obtener Mi Ubicación';
                    document.getElementById('obtenerUbicacion').disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    }
    
    // Guardar ubicación en el servidor
    function guardarUbicacion() {
        if (!currentPosition) {
            alert('Primero debe obtener su ubicación');
            return;
        }
        
        document.getElementById('guardarUbicacion').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        document.getElementById('guardarUbicacion').disabled = true;
        
        // Enviar datos al servidor
        fetch('{{ route("ubicaciones.actualizar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                latitud: currentPosition.lat,
                longitud: currentPosition.lng,
                direccion: currentPosition.direccion || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            alert('Ubicación guardada correctamente');
            document.getElementById('guardarUbicacion').innerHTML = '<i class="fas fa-save"></i> Guardar Ubicación';
            document.getElementById('guardarUbicacion').disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la ubicación');
            document.getElementById('guardarUbicacion').innerHTML = '<i class="fas fa-save"></i> Guardar Ubicación';
            document.getElementById('guardarUbicacion').disabled = false;
        });
    }
    
    // Cargar lista de clientes
    function cargarClientes() {
        fetch('{{ route("api.clientes") }}')
            .then(response => response.json())
            .then(data => {
                const clienteSelect = document.getElementById('cliente');
                
                data.forEach(cliente => {
                    const option = document.createElement('option');
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error cargando clientes:', error));
    }
    
    // Registrar actividad
    function registrarActividad(event) {
        event.preventDefault();
        
        const incluirUbicacion = document.getElementById('incluirUbicacion').checked;
        
        if (incluirUbicacion && !currentPosition) {
            alert('Debe obtener su ubicación actual para incluirla en el registro');
            return;
        }
        
        const formData = {
            tipo_actividad: document.getElementById('tipoActividad').value,
            cliente_id: document.getElementById('cliente').value,
            descripcion: document.getElementById('descripcion').value,
            resultado: document.getElementById('resultado').value
        };
        
        if (incluirUbicacion) {
            formData.latitud = currentPosition.lat;
            formData.longitud = currentPosition.lng;
            formData.direccion = currentPosition.direccion || '';
        }
        
        // Enviar datos al servidor
        fetch('{{ route("actividades.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert('Actividad registrada correctamente');
            document.getElementById('activityForm').reset();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al registrar la actividad');
        });
    }
</script>
@endsection 