@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Gestión de Agentes</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Administre los agentes y asigne la base para cada uno.
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Agentes</h5>
                                    <h3 class="card-text">{{ count($clients) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Fecha</h5>
                                    <h3 class="card-text">{{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">Base Total</h5>
                                    <h3 class="card-text">{{ number_format($clients->sum('base_total'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cartera</th>
                                    <th>País</th>
                                    <th>Ciudad</th>
                                    <th>Base</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->name }} {{ $client->last_name }}</td>
                                        <td>{{ $client->wallet_name }}</td>
                                        <td>{{ $client->country }}</td>
                                        <td>{{ $client->address }}</td>
                                        <td>{{ number_format($client->base_total, 2) }}</td>
                                        <td>
                                            <a href="{{ route('supervisor.agent.edit', $client->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-money-bill"></i> Modificar Base
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No hay agentes asignados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
