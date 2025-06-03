@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Reportes</h4>
                    <div>
                        <a href="{{ route('pymes.reportes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Reporte
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Formato</th>
                                    <th>Creado por</th>
                                    <th>Última ejecución</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($reportes) > 0)
                                    @foreach($reportes as $reporte)
                                    <tr>
                                        <td>{{ $reporte->id }}</td>
                                        <td>{{ $reporte->name }}</td>
                                        <td>{{ ucfirst($reporte->report_type) }}</td>
                                        <td>{{ strtoupper($reporte->output_format) }}</td>
                                        <td>{{ $reporte->creator ? $reporte->creator->name : 'N/A' }}</td>
                                        <td>{{ $reporte->last_run_at ? date('d/m/Y H:i', strtotime($reporte->last_run_at)) : 'Nunca' }}</td>
                                        <td>
                                            @if($reporte->status == 'active')
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pymes.reportes.show', $reporte->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('pymes.reportes.show', $reporte->id) }}?execute=1" class="btn btn-sm btn-success">
                                                <i class="fas fa-play"></i> Ejecutar
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No hay reportes disponibles</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reportes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 