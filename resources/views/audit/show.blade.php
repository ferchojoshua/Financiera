@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalle de Registro de Auditoría #{{ $audit->id }}</h5>
                        <a href="{{ route('audit.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Información Principal</h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row">
                                        <dt class="col-sm-4">ID:</dt>
                                        <dd class="col-sm-8">{{ $audit->id }}</dd>
                                        
                                        <dt class="col-sm-4">Usuario:</dt>
                                        <dd class="col-sm-8">{{ $audit->user ? $audit->user->name : 'Sistema' }}</dd>
                                        
                                        <dt class="col-sm-4">Acción:</dt>
                                        <dd class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $audit->action)) }}</dd>
                                        
                                        <dt class="col-sm-4">Descripción:</dt>
                                        <dd class="col-sm-8">{{ $audit->description }}</dd>
                                        
                                        <dt class="col-sm-4">Fecha:</dt>
                                        <dd class="col-sm-8">{{ $audit->created_at->format('d/m/Y H:i:s') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Información del Dispositivo</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($audit->decoded_device) && is_array($audit->decoded_device))
                                        <dl class="row">
                                            @foreach($audit->decoded_device as $key => $value)
                                                <dt class="col-sm-4">{{ ucfirst($key) }}:</dt>
                                                <dd class="col-sm-8">{{ $value }}</dd>
                                            @endforeach
                                        </dl>
                                    @else
                                        <p>{{ $audit->device }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Datos Adicionales</h6>
                                </div>
                                <div class="card-body">
                                    @if(isset($audit->decoded_data) && is_array($audit->decoded_data))
                                        <pre class="bg-light p-3"><code>{{ json_encode($audit->decoded_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    @elseif($audit->data)
                                        <pre class="bg-light p-3"><code>{{ $audit->data }}</code></pre>
                                    @else
                                        <p class="text-muted">No hay datos adicionales disponibles</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 