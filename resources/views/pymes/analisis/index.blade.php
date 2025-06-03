@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Análisis y Scoring Crediticio</h4>
                    <div>
                        <a href="{{ route('pymes.solicitudes') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> Ver Solicitudes
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
                                    <th>Cliente</th>
                                    <th>Solicitud</th>
                                    <th>Score</th>
                                    <th>Nivel de Riesgo</th>
                                    <th>Recomendación</th>
                                    <th>Analista</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($scorings) > 0)
                                    @foreach($scorings as $scoring)
                                    <tr>
                                        <td>{{ $scoring->id }}</td>
                                        <td>{{ $scoring->user->business_name ?? $scoring->user->name }}</td>
                                        <td>
                                            <a href="{{ route('pymes.solicitudes.show', $scoring->loan_application_id) }}">
                                                #{{ $scoring->loan_application_id }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($scoring->score, 2) }}</td>
                                        <td>
                                            @if($scoring->risk_level == 'very_low')
                                                <span class="badge bg-success">Muy Bajo</span>
                                            @elseif($scoring->risk_level == 'low')
                                                <span class="badge bg-info">Bajo</span>
                                            @elseif($scoring->risk_level == 'medium')
                                                <span class="badge bg-warning">Medio</span>
                                            @elseif($scoring->risk_level == 'high')
                                                <span class="badge bg-danger">Alto</span>
                                            @elseif($scoring->risk_level == 'very_high')
                                                <span class="badge bg-dark">Muy Alto</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($scoring->recommendation == 'approve')
                                                <span class="badge bg-success">Aprobar</span>
                                            @elseif($scoring->recommendation == 'reject')
                                                <span class="badge bg-danger">Rechazar</span>
                                            @elseif($scoring->recommendation == 'review')
                                                <span class="badge bg-warning">Revisar</span>
                                            @endif
                                        </td>
                                        <td>{{ $scoring->analyst->name ?? 'No asignado' }}</td>
                                        <td>{{ $scoring->calculation_date ? date('d/m/Y', strtotime($scoring->calculation_date)) : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('pymes.analisis.show', $scoring->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center">No hay análisis de crédito registrados</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $scorings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 