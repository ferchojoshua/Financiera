@extends('layouts.app')

@section('title', 'Mapa de Ubicación')

@section('styles')
<style>
    #mapa {
        height: 600px;
        width: 100%;
        border-radius: 8px;
    }
    .agent-list {
        height: 600px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
    }
    .agent-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .agent-item:hover {
        background-color: #f8f9fa;
    }
    .agent-item.active {
        background-color: #e9f7fe;
        font-weight: bold;
    }
    .info-window {
        max-width: 250px;
    }
    .info-window h5 {
        margin-top: 0;
        color: #007bff;
    }
    .last-update {
        font-size: 12px;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-map-marker-alt"></i> Mapa de Ubicación</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Mapa de Ubicación</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ubicación de Agentes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="refreshMap">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="mapa"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Agentes</h3>
                </div>
                <div class="card-body p-0">
                    <div class="agent-list">
                        @if(count($agentes) > 0)
                            @foreach($agentes as $agente)
                                <div class="agent-item" data-id="{{ $agente->id }}">
                                    <div><strong>{{ $agente->name }}</strong></div>
                                    <div class="text-muted">{{ $agente->email }}</div>
                                    <div class="last-update" id="last-update-{{ $agente->id }}"></div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted">
                                No hay agentes disponibles
                            </div>
                        @endif
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
    let markers = {};
    // Inicializar el mapa
    function initMap() {
        // Centrar en México por defecto
        map = new google.maps.Map(document.getElementById('mapa'), {
            center: {lat: 12.4379, lng: -86.8780},
            zoom: 13
        });
        
        // Cargar todas las ubicaciones
        cargarUbicaciones();
        
        // Configurar evento de click en la lista de agentes
        setupAgentListeners();
    }
    
    // Cargar ubicaciones de todos los agentes
    function cargarUbicaciones() {
        fetch('{{ route("ubicaciones.todas") }}')
            .then(response => response.json())
            .then(data => {
                // Limpiar marcadores existentes
                for (let key in markers) {
                    if (markers.hasOwnProperty(key)) {
                        markers[key].setMap(null);
                    }
                }
                markers = {};
                
                if (data.length > 0) {
                    data.forEach(ubicacion => {
                        agregarMarcador(ubicacion);
                        actualizarTiempoUbicacion(ubicacion.user_id, ubicacion.ultima_actualizacion);
                    });
                    
                    // Ajustar zoom para ver todos los marcadores
                    const bounds = new google.maps.LatLngBounds();
                    for (let key in markers) {
                        if (markers.hasOwnProperty(key)) {
                            bounds.extend(markers[key].getPosition());
                        }
                    }
                    map.fitBounds(bounds);
                }
            })
            .catch(error => console.error('Error cargando ubicaciones:', error));
    }
    
    // Agregar marcador al mapa
    function agregarMarcador(ubicacion) {
        const latLng = new google.maps.LatLng(
            parseFloat(ubicacion.latitud), 
            parseFloat(ubicacion.longitud)
        );
        
        const marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: ubicacion.nombre_agente,
            animation: google.maps.Animation.DROP
        });
        
        markers[ubicacion.user_id] = marker;
        
        const infowindow = new google.maps.InfoWindow({
            content: `
                <div class="info-window">
                    <h5>${ubicacion.nombre_agente}</h5>
                    <p><strong>Dirección:</strong> ${ubicacion.direccion || 'No disponible'}</p>
                    <p class="last-update">Última actualización: ${formatDateTime(ubicacion.ultima_actualizacion)}</p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infowindow.open(map, marker);
        });
    }
    
    // Configurar listeners para la lista de agentes
    function setupAgentListeners() {
        document.querySelectorAll('.agent-item').forEach(item => {
            item.addEventListener('click', function() {
                const agentId = this.getAttribute('data-id');
                
                // Resaltar agente seleccionado
                document.querySelectorAll('.agent-item').forEach(el => {
                    el.classList.remove('active');
                });
                this.classList.add('active');
                
                // Centrar mapa en el agente seleccionado
                if (markers[agentId]) {
                    map.setCenter(markers[agentId].getPosition());
                    map.setZoom(15);
                    
                    // Simular clic en el marcador
                    google.maps.event.trigger(markers[agentId], 'click');
                } else {
                    // Intentar cargar la ubicación del agente
                    fetch(`{{ url('ubicaciones') }}/${agentId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('No se encontró ubicación');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const ubicacion = {
                                user_id: data.user_id,
                                nombre_agente: this.querySelector('strong').textContent,
                                latitud: data.latitud,
                                longitud: data.longitud,
                                direccion: data.direccion,
                                ultima_actualizacion: data.ultima_actualizacion
                            };
                            
                            agregarMarcador(ubicacion);
                            actualizarTiempoUbicacion(data.user_id, data.ultima_actualizacion);
                            
                            map.setCenter(markers[agentId].getPosition());
                            map.setZoom(15);
                            
                            // Simular clic en el marcador
                            google.maps.event.trigger(markers[agentId], 'click');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('No hay ubicación disponible para este agente');
                        });
                }
            });
        });
        
        // Botón de actualización
        document.getElementById('refreshMap').addEventListener('click', cargarUbicaciones);
    }
    
    // Actualizar información de tiempo
    function actualizarTiempoUbicacion(userId, timestamp) {
        const element = document.getElementById(`last-update-${userId}`);
        if (element) {
            element.textContent = `Última actualización: ${formatDateTime(timestamp)}`;
        }
    }
    
    // Formatear fecha y hora
    function formatDateTime(dateTimeStr) {
        const date = new Date(dateTimeStr);
        return date.toLocaleString();
    }
</script>
@endsection 