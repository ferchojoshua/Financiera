@extends('layouts.app')

@section('admin-section')
<!-- Esta sección se mostrará en la parte superior para superadmins -->
<div class="col-12 mb-4">
    <div class="card bg-light">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fa fa-users"></i> Gestión de Clientes
            </h5>
            <p class="card-text">
                Este módulo permite administrar todos los clientes registrados en el sistema. Desde aquí puede visualizar, editar, 
                y gestionar los datos de sus clientes.
            </p>
            <a href="{{ url('client/create') }}" class="btn btn-sm btn-primary">
                <i class="fa fa-plus-circle"></i> Nuevo Cliente
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
    <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main">
        <div class="wrap">
            <section class="app-content">
                <div class="row">
                    <div class="col-md-12">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="widget p-lg">
                            <h4 class="m-b-lg">Detalles Clientes y Ventas</h4>
                            <table class="table client-table">
                                <thead class="visible-lg">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Barrio</th>
                                    <th>Total</th>
                                    <th>Pagados</th>
                                    <th>Vigentes</th>
                                    <th>Monto Prestado</th>
                                    <th>Monto Restante</th>
                                    <th>Tipo</th>
                                    <th>Accion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td><span class="value">{{$client->name}}</span></td>
                                        <td><span class="value">{{$client->last_name}}</span></td>
                                        <td><span class="value">{{$client->province}}</span></td>
                                        <td><span class="value">{{$client->credit_count}}</span></td>
                                        <td><span class="value">{{$client->closed}}</span></td>
                                        <td><span class="value">{{$client->inprogress}}</span></td>
                                        <td><span class="value">{{isset($client->amount_net) ? $client->amount_net->amount_neto +$client->gap_credit : 0}}</span></td>
                                        <td><span class="value">{{$client->summary_net + $client->gap_credit}}</span></td>
                                        <td>
                                            @if($client->status=='good')
                                                <span class="badge-info badge">BUENO</span>
                                            @elseif($client->status=='bad')
                                                <span class="badge-danger badge">MALO</span>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{url('client/create')}}?id={{$client->id}}" class="btn btn-success btn-xs">Venta</a>
                                            <a href="{{url('client')}}/{{$client->id}}" class="btn btn-info btn-xs">Datos</a>
                                            @if(isset($client->lat) && isset($client->lng))
                                                <a href="http://www.google.com/maps/place/{{$client->lat}},{{$client->lng}}" target="_blank" class="btn btn-info btn-xs">Ver Mapa</a>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                                </tbody></table>
                        </div><!-- .widget -->
                    </div>
                </div><!-- .row -->
            </section>
        </div>
    </main>
@endsection
