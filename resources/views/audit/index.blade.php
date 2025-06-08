@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Registros de Auditoría</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('audit.index') }}" method="GET" class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="user_id">Usuario</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Todos los usuarios</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="action">Acción</label>
                                    <select name="action" id="action" class="form-control">
                                        <option value="">Todas las acciones</option>
                                        @foreach($actions as $action)
                                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="date_from">Desde</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="date_to">Hasta</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label>&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('audit.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fa fa-file-excel"></i> Exportar a CSV
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>IP</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($audits as $audit)
                                    <tr>
                                        <td>{{ $audit->id }}</td>
                                        <td>{{ $audit->user ? $audit->user->name : 'Sistema' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($audit->description, 50)}}</td>
                                        <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @php
                                                $device = json_decode($audit->device, true);
                                                $ip = $device['ip'] ?? 'No disponible';
                                            @endphp
                                            {{ $ip }}
                                        </td>
                                        <td>
                                            <a href="{{ route('audit.show', $audit->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i> Ver detalles
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay registros de auditoría</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $audits->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 