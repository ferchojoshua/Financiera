@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Rendimiento de Clientes
                    </h4>
                </div>
                <div class="card-body">
                    <p>Esta sección mostrará un reporte detallado del rendimiento de los clientes.</p>
                    <p><em>(Funcionalidad en desarrollo)</em></p>

                    <h5 class="mt-4">Clientes con más créditos</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Nº de Créditos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td>{{ $client->name }} {{ $client->last_name }}</td>
                                    <td>{{ $client->email }}</td>
                                    <td>{{ $client->credits_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay datos para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 