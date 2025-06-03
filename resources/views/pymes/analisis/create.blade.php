@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Nuevo Análisis Crediticio</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Información de la Solicitud:</strong> 
                        <div class="mt-2">
                            <div><strong>Cliente:</strong> {{ $solicitud->client->business_name }}</div>
                            <div><strong>Monto Solicitado:</strong> € {{ number_format($solicitud->amount, 2, ',', '.') }}</div>
                            <div><strong>Plazo:</strong> {{ $solicitud->term_months }} meses</div>
                            <div><strong>Propósito:</strong> {{ $solicitud->purpose }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pymes.analisis.store') }}">
                        @csrf
                        
                        <input type="hidden" name="loan_application_id" value="{{ $solicitud->id }}">
                        <input type="hidden" name="user_id" value="{{ $solicitud->client_id }}">
                        
                        <h5 class="mb-3">Indicadores Financieros</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ratio_liquidez" class="form-label">Ratio de Liquidez</label>
                                    <input type="number" step="0.01" class="form-control @error('ratio_liquidez') is-invalid @enderror" 
                                        id="ratio_liquidez" name="financial_indicators[ratio_liquidez]" value="{{ old('financial_indicators.ratio_liquidez') }}">
                                    @error('financial_indicators.ratio_liquidez')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ratio_endeudamiento" class="form-label">Ratio de Endeudamiento (%)</label>
                                    <input type="number" step="0.01" class="form-control @error('ratio_endeudamiento') is-invalid @enderror" 
                                        id="ratio_endeudamiento" name="financial_indicators[ratio_endeudamiento]" value="{{ old('financial_indicators.ratio_endeudamiento') }}">
                                    @error('financial_indicators.ratio_endeudamiento')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rentabilidad" class="form-label">Rentabilidad (%)</label>
                                    <input type="number" step="0.01" class="form-control @error('rentabilidad') is-invalid @enderror" 
                                        id="rentabilidad" name="financial_indicators[rentabilidad]" value="{{ old('financial_indicators.rentabilidad') }}">
                                    @error('financial_indicators.rentabilidad')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cobertura_deuda" class="form-label">Cobertura de Deuda</label>
                                    <input type="number" step="0.01" class="form-control @error('cobertura_deuda') is-invalid @enderror" 
                                        id="cobertura_deuda" name="financial_indicators[cobertura_deuda]" value="{{ old('financial_indicators.cobertura_deuda') }}">
                                    @error('financial_indicators.cobertura_deuda')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">Factores Cualitativos</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="experiencia_sector" class="form-label">Experiencia en el Sector (años)</label>
                                    <input type="number" class="form-control @error('experiencia_sector') is-invalid @enderror" 
                                        id="experiencia_sector" name="qualitative_factors[experiencia_sector]" value="{{ old('qualitative_factors.experiencia_sector') }}">
                                    @error('qualitative_factors.experiencia_sector')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="calidad_gestion" class="form-label">Calidad de la Gestión (1-10)</label>
                                    <input type="number" min="1" max="10" class="form-control @error('calidad_gestion') is-invalid @enderror" 
                                        id="calidad_gestion" name="qualitative_factors[calidad_gestion]" value="{{ old('qualitative_factors.calidad_gestion') }}">
                                    @error('qualitative_factors.calidad_gestion')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="posicion_mercado" class="form-label">Posición en el Mercado</label>
                                    <select class="form-control @error('posicion_mercado') is-invalid @enderror" 
                                        id="posicion_mercado" name="qualitative_factors[posicion_mercado]">
                                        <option value="">Seleccionar...</option>
                                        <option value="lider" {{ old('qualitative_factors.posicion_mercado') == 'lider' ? 'selected' : '' }}>Líder</option>
                                        <option value="fuerte" {{ old('qualitative_factors.posicion_mercado') == 'fuerte' ? 'selected' : '' }}>Fuerte</option>
                                        <option value="media" {{ old('qualitative_factors.posicion_mercado') == 'media' ? 'selected' : '' }}>Media</option>
                                        <option value="debil" {{ old('qualitative_factors.posicion_mercado') == 'debil' ? 'selected' : '' }}>Débil</option>
                                    </select>
                                    @error('qualitative_factors.posicion_mercado')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">Datos de Bureaus Externos</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scoring_externo" class="form-label">Scoring Externo (1-10)</label>
                                    <input type="number" min="1" max="10" class="form-control @error('scoring_externo') is-invalid @enderror" 
                                        id="scoring_externo" name="external_bureau_data[scoring_externo]" value="{{ old('external_bureau_data.scoring_externo') }}">
                                    @error('external_bureau_data.scoring_externo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="incidencias_pago" class="form-label">Incidencias de Pago</label>
                                    <input type="number" min="0" class="form-control @error('incidencias_pago') is-invalid @enderror" 
                                        id="incidencias_pago" name="external_bureau_data[incidencias_pago]" value="{{ old('external_bureau_data.incidencias_pago', 0) }}">
                                    @error('external_bureau_data.incidencias_pago')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="deuda_externa" class="form-label">Deuda Externa (€)</label>
                                    <input type="number" min="0" step="1" class="form-control @error('deuda_externa') is-invalid @enderror" 
                                        id="deuda_externa" name="external_bureau_data[deuda_externa]" value="{{ old('external_bureau_data.deuda_externa', 0) }}">
                                    @error('external_bureau_data.deuda_externa')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-3">Resultados del Análisis</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="score" class="form-label">Score Final (0-100)</label>
                                    <input type="number" min="0" max="100" step="0.01" class="form-control @error('score') is-invalid @enderror" 
                                        id="score" name="score" value="{{ old('score') }}" required>
                                    @error('score')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="risk_level" class="form-label">Nivel de Riesgo</label>
                                    <select class="form-control @error('risk_level') is-invalid @enderror" id="risk_level" name="risk_level" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="very_low" {{ old('risk_level') == 'very_low' ? 'selected' : '' }}>Muy Bajo</option>
                                        <option value="low" {{ old('risk_level') == 'low' ? 'selected' : '' }}>Bajo</option>
                                        <option value="medium" {{ old('risk_level') == 'medium' ? 'selected' : '' }}>Medio</option>
                                        <option value="high" {{ old('risk_level') == 'high' ? 'selected' : '' }}>Alto</option>
                                        <option value="very_high" {{ old('risk_level') == 'very_high' ? 'selected' : '' }}>Muy Alto</option>
                                    </select>
                                    @error('risk_level')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="recommendation" class="form-label">Recomendación</label>
                                    <select class="form-control @error('recommendation') is-invalid @enderror" id="recommendation" name="recommendation" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="approve" {{ old('recommendation') == 'approve' ? 'selected' : '' }}>Aprobar</option>
                                        <option value="reject" {{ old('recommendation') == 'reject' ? 'selected' : '' }}>Rechazar</option>
                                        <option value="review" {{ old('recommendation') == 'review' ? 'selected' : '' }}>Revisar</option>
                                    </select>
                                    @error('recommendation')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scoring_model" class="form-label">Modelo de Scoring Utilizado</label>
                                    <select class="form-control @error('scoring_model') is-invalid @enderror" id="scoring_model" name="scoring_model" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="standard" {{ old('scoring_model') == 'standard' ? 'selected' : '' }}>Estándar</option>
                                        <option value="advanced" {{ old('scoring_model') == 'advanced' ? 'selected' : '' }}>Avanzado</option>
                                        <option value="sector_specific" {{ old('scoring_model') == 'sector_specific' ? 'selected' : '' }}>Específico del Sector</option>
                                        <option value="custom" {{ old('scoring_model') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                    </select>
                                    @error('scoring_model')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notas y Observaciones</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-3 text-center">
                                <button type="submit" class="btn btn-primary me-2">
                                    Guardar Análisis
                                </button>
                                <a href="{{ route('pymes.solicitudes.show', $solicitud->id) }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 