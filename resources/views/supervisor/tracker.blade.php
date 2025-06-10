@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0 font-weight-bold"><i class="fa fa-map-marker-alt mr-2"></i>Rastreo de Agentes</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-left-4 shadow-sm">
                        <i class="fa fa-info-circle mr-2"></i> Monitoree la actividad y ubicación de los agentes.
                    </div>
                    
                    <form action="{{ url('supervisor/tracker/search') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="agent" class="font-weight-bold">Seleccione Agente</label>
                                <select name="agent_id" id="agent" class="form-control">
                                    <option value="">-- Seleccione un agente --</option>
                                    @foreach(\App\Models\User::where('level', 'agent')->orderBy('name')->get() as $agent)
                                        <option value="{{ $agent->id }}" {{ isset($_GET['agent_id']) && $_GET['agent_id'] == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="date" class="font-weight-bold">Fecha</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search mr-1"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    @php
                    $agentId = isset($_GET['agent_id']) ? $_GET['agent_id'] : null;
                    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
                    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d H:i:s');
                    
                    $agent = $agentId ? \App\Models\User::find($agentId) : null;
                    
                    // Rutas y ubicaciones
                    $routes = $agentId ? \App\Models\Route::where('id_agent', $agentId)
                        ->whereDate('created_at', $startDate)
                        ->orderBy('created_at', 'asc')
                        ->get() : collect([]);
                        
                    // Actividades (pagos, visitas, etc.)
                    $paymentsCollection = $agentId ? \App\Models\Payment::where('id_agent', $agentId)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->get() : collect([]);
                        
                    $totalPayments = $paymentsCollection->sum('amount');
                        
                    // Unir todas las actividades
                    $activities = $paymentsCollection->map(function($item) {
                        return [
                            'time' => $item->created_at,
                            'type' => 'payment',
                            'details' => 'Pago registrado por $' . number_format($item->amount, 2),
                            'client_id' => $item->credit->client_id ?? null
                        ];
                    });
                    @endphp
                    
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 font-weight-bold"><i class="fa fa-map mr-2"></i>Mapa de Ubicación</h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="map" style="height: 400px; width: 100%; border-radius: 0 0 8px 8px;">
                                @if($agentId && count($routes) > 0)
                                    <div id="map-container" style="height: 100%; width: 100%;"></div>
                                @else
                                    <div class="text-center p-5 bg-light" style="height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 0 0 8px 8px;">
                                        <div>
                                            <i class="fa fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                            <p class="lead">Seleccione un agente para ver su ubicación</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 font-weight-bold"><i class="fa fa-clipboard-list mr-2"></i>Registro de Actividades</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Hora</th>
                                            <th>Actividad</th>
                                            <th>Cliente/Ubicación</th>
                                            <th>Detalles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($agentId && $activities->count() > 0)
                                            @foreach($activities as $activity)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($activity['time'])->format('H:i:s') }}</td>
                                                <td>
                                                    @if($activity['type'] == 'payment')
                                                        <span class="badge badge-success text-white">Pago</span>
                                                    @elseif($activity['type'] == 'visit')
                                                        <span class="badge badge-primary text-white">Visita</span>
                                                    @elseif($activity['type'] == 'route')
                                                        <span class="badge badge-info text-white">Ruta</span>
                                                    @else
                                                        <span class="badge badge-secondary text-white">Otro</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($activity['client_id']))
                                                        @php
                                                        $client = \App\Models\User::find($activity['client_id']);
                                                        @endphp
                                                        {{ $client ? $client->name : 'Cliente #' . $activity['client_id'] }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $activity['details'] }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fa fa-exclamation-circle fa-2x mb-3"></i>
                                                    <p>No hay actividades registradas para este agente en la fecha seleccionada</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($agentId && count($routes) > 0)
@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', '') }}&callback=initMap" async defer></script>
<script>
    function initMap() {
        const mapContainer = document.getElementById('map-container');
        
        // Coordenadas de las rutas
        const routeCoordinates = [
            @foreach($routes as $route)
                {lat: {{ $route->lat }}, lng: {{ $route->lng }}},
            @endforeach
        ];
        
        // Crear mapa centrado en la primera ubicación o en una ubicación predeterminada
        const map = new google.maps.Map(mapContainer, {
            zoom: 14,
            center: routeCoordinates.length > 0 ? routeCoordinates[0] : {lat: 0, lng: 0},
            mapTypeId: 'roadmap',
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
            },
            fullscreenControl: true
        });
        
        // Crear marcadores para cada punto
        routeCoordinates.forEach((coordinate, index) => {
            const marker = new google.maps.Marker({
                position: coordinate,
                map: map,
                title: `Punto ${index + 1}`,
                label: `${index + 1}`
            });
            
            // Agregar evento de clic si se desea mostrar información adicional
            marker.addListener('click', function() {
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div class="p-2"><strong>Punto ${index + 1}</strong><br>Ubicación registrada</div>`
                });
                infoWindow.open(map, marker);
            });
        });
        
        // Crear línea de ruta
        const routePath = new google.maps.Polyline({
            path: routeCoordinates,
            geodesic: true,
            strokeColor: "#007bff",
            strokeOpacity: 1.0,
            strokeWeight: 3
        });
        
        routePath.setMap(map);
    }
</script>
@endsection
@endif
@endsection 