@extends('layouts.app')

@section('content')
    <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main">
        <div class="wrap">
            <section class="app-content">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="widget card">
                            <div class="card-header bg-success text-white">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h4 class="m-0">
                                            <i class="fa fa-users"></i> Gestión de Clientes
                                        </h4>
                                        <small>Administración de clientes y expedientes</small>
                                    </div>
                                    <div class="col-4 text-right">
                                        <a href="{{ route('client.create') }}" class="btn btn-light btn-sm">
                                            <i class="fa fa-plus-circle"></i> Nuevo Cliente
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Filtros -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="branch">Sucursal</label>
                                            <select class="form-control" id="branch" name="branch">
                                                <option value="">Todas las sucursales</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientType">Tipo de Cliente</label>
                                            <select class="form-control" id="clientType" name="clientType">
                                                <option value="">Todos los tipos</option>
                                                @foreach($clientTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search">Buscar</label>
                                            <input type="text" class="form-control" id="search" name="search" placeholder="Nombre, teléfono, DUI...">
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Clientes</h5>
                                                <h3 class="card-text">{{ $totalClients }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Créditos Activos</h5>
                                                <h3 class="card-text">{{ $activeCredits }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de clientes -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover client-table">
                                        <thead>
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
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($clients as $client)
                                                <tr>
                                                    <td>{{ $client->name }}</td>
                                                    <td>{{ $client->last_name }}</td>
                                                    <td>{{ $client->province }}</td>
                                                    <td>{{ $client->total_credits }}</td>
                                                    <td>{{ $client->paid_credits }}</td>
                                                    <td>{{ $client->active_credits }}</td>
                                                    <td>${{ number_format($client->total_borrowed, 2) }}</td>
                                                    <td>${{ number_format($client->remaining_amount, 2) }}</td>
                                                    <td>
                                                        @if($client->client_type)
                                                            <span class="badge badge-info">{{ $client->client_type->name }}</span>
                                                        @else
                                                            <span class="badge badge-secondary">No definido</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('client.show', $client->id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('client.edit', $client->id) }}" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection

@push('styles')
<style>
.bg-success {
    background-color: #10775c !important;
}
.btn-success {
    background-color: #10775c !important;
    border-color: #10775c !important;
}
.btn-success:hover {
    background-color: #0a5640 !important;
    border-color: #0a5640 !important;
}
.card-header {
    padding: 1rem;
}
.value {
    font-weight: 500;
}
.table-responsive {
    overflow-x: auto;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Cambio de sucursal
    $('#branch').change(function() {
        window.location.href = '{{ route("client.change_branch") }}?branch_id=' + $(this).val();
    });

    // Filtro por tipo de cliente
    $('#clientType').change(function() {
        filterClients();
    });

    // Búsqueda
    $('#search').keyup(function() {
        filterClients();
    });

    function filterClients() {
        var type = $('#clientType').val();
        var search = $('#search').val();
        
        $.get('{{ route("client.filter") }}', {
            type: type,
            search: search
        }, function(data) {
            // Actualizar la tabla con los resultados
            updateTable(data);
        });
    }

    function updateTable(data) {
        var tbody = $('.client-table tbody');
        tbody.empty();
        
        data.forEach(function(client) {
            var row = '<tr>' +
                '<td>' + client.name + '</td>' +
                '<td>' + client.last_name + '</td>' +
                '<td>' + client.province + '</td>' +
                '<td>' + client.total_credits + '</td>' +
                '<td>' + client.paid_credits + '</td>' +
                '<td>' + client.active_credits + '</td>' +
                '<td>$' + formatNumber(client.total_borrowed) + '</td>' +
                '<td>$' + formatNumber(client.remaining_amount) + '</td>' +
                '<td>' + getClientTypeBadge(client.client_type) + '</td>' +
                '<td>' + getActionButtons(client.id) + '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    function formatNumber(number) {
        return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    function getClientTypeBadge(type) {
        if (type) {
            return '<span class="badge badge-info">' + type.name + '</span>';
        }
        return '<span class="badge badge-secondary">No definido</span>';
    }

    function getActionButtons(id) {
        return '<div class="btn-group">' +
            '<a href="/client/' + id + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>' +
            '<a href="/client/' + id + '/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>' +
            '</div>';
    }
});
</script>
@endpush
