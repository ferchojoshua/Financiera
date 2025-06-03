@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Análisis Crediticio</h4>
                    <div>
                        <a href="{{ route('pymes.analisis') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="{{ route('pymes.solicitudes.show', $analisis->loan_application_id) }}" class="btn btn-info">
                            <i class="fas fa-file-alt"></i> Ver Solicitud
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3>Scoring #{{ $analisis->id }}</h3>
                            <div class="d-flex align-items-center mt-2">
                                <span class="me-3">Nivel de Riesgo:</span> 
                                <span class="badge 
                                    @if($analisis->risk_level == 'very_low') bg-success 
                                    @elseif($analisis->risk_level == 'low') bg-info
                                    @elseif($analisis->risk_level == 'medium') bg-warning
                                    @elseif($analisis->risk_level == 'high') bg-danger
                                    @else bg-dark @endif px-3 py-2">
                                    @if($analisis->risk_level == 'very_low') Muy Bajo
                                    @elseif($analisis->risk_level == 'low') Bajo
                                    @elseif($analisis->risk_level == 'medium') Medio
                                    @elseif($analisis->risk_level == 'high') Alto
                                    @elseif($analisis->risk_level == 'very_high') Muy Alto
                                    @endif
                                </span>

                                <span class="mx-4">Recomendación:</span>
                                <span class="badge 
                                    @if($analisis->recommendation == 'approve') bg-success 
                                    @elseif($analisis->recommendation == 'reject') bg-danger
                                    @else bg-warning @endif px-3 py-2">
                                    @if($analisis->recommendation == 'approve') Aprobar
                                    @elseif($analisis->recommendation == 'reject') Rechazar
                                    @else Revisar
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Cliente:</div>
                        <div class="col-md-9">{{ $analisis->user->business_name ?? $analisis->user->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Solicitud:</div>
                        <div class="col-md-9">
                            <a href="{{ route('pymes.solicitudes.show', $analisis->loan_application_id) }}">
                                #{{ $analisis->loan_application_id }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Score Final:</div>
                        <div class="col-md-9">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar 
                                    @if($analisis->score >= 80) bg-success
                                    @elseif($analisis->score >= 60) bg-info
                                    @elseif($analisis->score >= 40) bg-warning
                                    @else bg-danger @endif"
                                    role="progressbar" style="width: {{ $analisis->score }}%;" 
                                    aria-valuenow="{{ $analisis->score }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($analisis->score, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Modelo de Scoring:</div>
                        <div class="col-md-9">
                            @if($analisis->scoring_model == 'standard') Estándar
                            @elseif($analisis->scoring_model == 'advanced') Avanzado
                            @elseif($analisis->scoring_model == 'sector_specific') Específico del Sector
                            @elseif($analisis->scoring_model == 'custom') Personalizado
                            @else {{ $analisis->scoring_model }}
                            @endif
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3">Indicadores Financieros</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Indicador</th>
                                            <th>Valor</th>
                                            <th>Interpretación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $financialIndicators = json_decode($analisis->financial_indicators, true);
                                        @endphp
                                        
                                        @if(isset($financialIndicators['ratio_liquidez']))
                                        <tr>
                                            <td>Ratio de Liquidez</td>
                                            <td>{{ $financialIndicators['ratio_liquidez'] }}</td>
                                            <td>
                                                @if($financialIndicators['ratio_liquidez'] >= 1.5)
                                                    <span class="text-success">Excelente</span>
                                                @elseif($financialIndicators['ratio_liquidez'] >= 1.0)
                                                    <span class="text-info">Bueno</span>
                                                @elseif($financialIndicators['ratio_liquidez'] >= 0.8)
                                                    <span class="text-warning">Regular</span>
                                                @else
                                                    <span class="text-danger">Preocupante</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($financialIndicators['ratio_endeudamiento']))
                                        <tr>
                                            <td>Ratio de Endeudamiento</td>
                                            <td>{{ $financialIndicators['ratio_endeudamiento'] }}%</td>
                                            <td>
                                                @if($financialIndicators['ratio_endeudamiento'] <= 40)
                                                    <span class="text-success">Bajo</span>
                                                @elseif($financialIndicators['ratio_endeudamiento'] <= 60)
                                                    <span class="text-info">Moderado</span>
                                                @elseif($financialIndicators['ratio_endeudamiento'] <= 80)
                                                    <span class="text-warning">Alto</span>
                                                @else
                                                    <span class="text-danger">Excesivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($financialIndicators['rentabilidad']))
                                        <tr>
                                            <td>Rentabilidad</td>
                                            <td>{{ $financialIndicators['rentabilidad'] }}%</td>
                                            <td>
                                                @if($financialIndicators['rentabilidad'] >= 15)
                                                    <span class="text-success">Excelente</span>
                                                @elseif($financialIndicators['rentabilidad'] >= 8)
                                                    <span class="text-info">Buena</span>
                                                @elseif($financialIndicators['rentabilidad'] >= 3)
                                                    <span class="text-warning">Regular</span>
                                                @else
                                                    <span class="text-danger">Baja</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($financialIndicators['cobertura_deuda']))
                                        <tr>
                                            <td>Cobertura de Deuda</td>
                                            <td>{{ $financialIndicators['cobertura_deuda'] }}</td>
                                            <td>
                                                @if($financialIndicators['cobertura_deuda'] >= 2)
                                                    <span class="text-success">Excelente</span>
                                                @elseif($financialIndicators['cobertura_deuda'] >= 1.5)
                                                    <span class="text-info">Buena</span>
                                                @elseif($financialIndicators['cobertura_deuda'] >= 1.0)
                                                    <span class="text-warning">Ajustada</span>
                                                @else
                                                    <span class="text-danger">Insuficiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Factores Cualitativos</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    @php
                                        $qualitativeFactors = json_decode($analisis->qualitative_factors, true) ?? [];
                                    @endphp
                                    <tbody>
                                        @if(isset($qualitativeFactors['experiencia_sector']))
                                        <tr>
                                            <td width="30%"><strong>Experiencia en el Sector</strong></td>
                                            <td>{{ $qualitativeFactors['experiencia_sector'] }} años</td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($qualitativeFactors['calidad_gestion']))
                                        <tr>
                                            <td><strong>Calidad de la Gestión</strong></td>
                                            <td>{{ $qualitativeFactors['calidad_gestion'] }}/10</td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($qualitativeFactors['posicion_mercado']))
                                        <tr>
                                            <td><strong>Posición en el Mercado</strong></td>
                                            <td>
                                                @if($qualitativeFactors['posicion_mercado'] == 'lider')
                                                    Líder
                                                @elseif($qualitativeFactors['posicion_mercado'] == 'fuerte')
                                                    Fuerte
                                                @elseif($qualitativeFactors['posicion_mercado'] == 'media')
                                                    Media
                                                @elseif($qualitativeFactors['posicion_mercado'] == 'debil')
                                                    Débil
                                                @else
                                                    {{ $qualitativeFactors['posicion_mercado'] }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Datos de Bureaus Externos</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    @php
                                        $externalData = json_decode($analisis->external_bureau_data, true) ?? [];
                                    @endphp
                                    <tbody>
                                        @if(isset($externalData['scoring_externo']))
                                        <tr>
                                            <td width="30%"><strong>Scoring Externo</strong></td>
                                            <td>{{ $externalData['scoring_externo'] }}/10</td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($externalData['incidencias_pago']))
                                        <tr>
                                            <td><strong>Incidencias de Pago</strong></td>
                                            <td>{{ $externalData['incidencias_pago'] }}</td>
                                        </tr>
                                        @endif
                                        
                                        @if(isset($externalData['deuda_externa']))
                                        <tr>
                                            <td><strong>Deuda Externa</strong></td>
                                            <td>€ {{ number_format($externalData['deuda_externa'], 2, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($analisis->notes)
                    <h5 class="mb-3">Notas y Observaciones</h5>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    {{ $analisis->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Analista:</div>
                        <div class="col-md-9">{{ $analisis->analyst->name ?? 'No asignado' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Fecha de Cálculo:</div>
                        <div class="col-md-9">{{ $analisis->calculation_date ? date('d/m/Y H:i', strtotime($analisis->calculation_date)) : 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Fecha de Creación:</div>
                        <div class="col-md-9">{{ $analisis->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            @if($analisis->recommendation == 'approve')
                                <a href="#" class="btn btn-success me-2">
                                    <i class="fas fa-check-circle"></i> Aprobar Crédito
                                </a>
                            @elseif($analisis->recommendation == 'reject')
                                <a href="#" class="btn btn-danger me-2">
                                    <i class="fas fa-times-circle"></i> Rechazar Solicitud
                                </a>
                            @else
                                <a href="#" class="btn btn-warning me-2">
                                    <i class="fas fa-search"></i> Solicitar Información Adicional
                                </a>
                            @endif
                            
                            <a href="#" class="btn btn-secondary">
                                <i class="fas fa-print"></i> Imprimir Informe
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 