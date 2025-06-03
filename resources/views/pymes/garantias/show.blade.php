@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles de la Garantía</h4>
                    <div>
                        <a href="{{ route('pymes.garantias') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3>Garantía #{{ $garantia->id }}</h3>
                            <span class="badge 
                                @if($garantia->status == 'active') bg-success 
                                @elseif($garantia->status == 'pending') bg-warning 
                                @elseif($garantia->status == 'executed') bg-danger 
                                @else bg-secondary @endif">
                                @if($garantia->status == 'active') Activa
                                @elseif($garantia->status == 'pending') Pendiente
                                @elseif($garantia->status == 'executed') Ejecutada
                                @else {{ $garantia->status }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Cliente:</div>
                        <div class="col-md-9">{{ $garantia->user->business_name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Tipo de Garantía:</div>
                        <div class="col-md-9">
                            @if($garantia->type == 'real_estate') Inmueble
                            @elseif($garantia->type == 'vehicle') Vehículo
                            @elseif($garantia->type == 'machinery') Maquinaria
                            @elseif($garantia->type == 'deposit') Depósito
                            @elseif($garantia->type == 'inventory') Inventario
                            @elseif($garantia->type == 'personal') Personal (Aval)
                            @else {{ $garantia->type }}
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Descripción:</div>
                        <div class="col-md-9">{{ $garantia->description }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Valor Estimado:</div>
                        <div class="col-md-9">€ {{ number_format($garantia->value, 2, ',', '.') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Crédito Asociado:</div>
                        <div class="col-md-9">
                            @if($garantia->credit)
                                <a href="#">Crédito #{{ $garantia->credit->id }}</a> - € {{ number_format($garantia->credit->amount, 2, ',', '.') }}
                            @else
                                <span class="text-muted">Sin crédito asignado</span>
                            @endif
                        </div>
                    </div>

                    @if($garantia->document_path)
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Documento:</div>
                        <div class="col-md-9">
                            <a href="{{ asset('storage/' . $garantia->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-download"></i> Ver documento
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($garantia->verified_by)
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Verificado por:</div>
                        <div class="col-md-9">{{ $garantia->verifier->name }} el {{ $garantia->verification_date->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif

                    @if($garantia->notes)
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Notas:</div>
                        <div class="col-md-9">{{ $garantia->notes }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Fecha de Registro:</div>
                        <div class="col-md-9">{{ $garantia->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Última Actualización:</div>
                        <div class="col-md-9">{{ $garantia->updated_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <hr>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Acciones</h5>
                            <div class="mt-3">
                                @if($garantia->status == 'pending')
                                <button class="btn btn-success me-2">
                                    <i class="fas fa-check"></i> Verificar Garantía
                                </button>
                                @endif
                                
                                @if($garantia->status != 'executed')
                                <button class="btn btn-danger me-2">
                                    <i class="fas fa-gavel"></i> Ejecutar Garantía
                                </button>
                                @endif
                                
                                @if(!$garantia->credit_id)
                                <button class="btn btn-primary me-2">
                                    <i class="fas fa-link"></i> Asignar a Crédito
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 