@extends('layouts.app')

@section('title', 'Acceso Denegado')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Acceso Denegado</h3>
                </div>
                <div class="card-body text-center">
                    <div class="my-5">
                        <i class="fas fa-lock fa-5x text-danger mb-4"></i>
                        <h4 class="mb-4">No tienes permisos para acceder a este módulo</h4>
                        
                        @if(isset($module))
                            <p class="lead">Módulo: <strong>{{ ucfirst($module) }}</strong></p>
                        @endif
                        
                        <p class="text-muted">Si crees que esto es un error, contacta al administrador del sistema.</p>
                        
                        <div class="mt-5">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-2"></i>Volver al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 