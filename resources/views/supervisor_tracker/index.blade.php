@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
                    <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Rastreo de Agentes</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Seleccione un agente y una fecha para consultar su actividad diaria.
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
                                        <h5 class="card-title">Semana</h5>
                                        <h3 class="card-text">{{ \Carbon\Carbon::parse($today)->weekOfYear }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Cartera</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                        <tr>
                                            <td>{{ $client->id }}</td>
                                            <td>{{ $client->name }} {{ $client->last_name }}</td>
                                            <td>{{ $client->email }}</td>
                                            <td>{{ $client->wallet_name }}</td>
                                            <td>
                                                @if($client->active_user == 1)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-{{ $client->id }}">
                                                        <i class="fa fa-search"></i> Rastrear
                                                    </button>
                                                    <a href="{{ route('review.create', ['id_agent' => $client->id]) }}" class="btn btn-info btn-sm">
                                                        <i class="fa fa-clipboard-list"></i> Revisión
                                                    </a>
                                                </div>

                                                <!-- Modal de Rastreo -->
                                                <div class="modal fade" id="modal-{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel-{{ $client->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title" id="modalLabel-{{ $client->id }}">Rastreo de {{ $client->name }} {{ $client->last_name }}</h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('tracker.show', $client->id) }}" method="GET">
                                                                    <div class="form-group">
                                                                        <label for="date_start">Fecha a consultar:</label>
                                                                        <input type="text" class="form-control datepicker" id="date_start" name="date_start" value="{{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}" required>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <button type="submit" class="btn btn-primary">
                                                                            <i class="fa fa-search"></i> Consultar
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay agentes asignados a este supervisor</td>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        });
    });
</script>
@endsection
