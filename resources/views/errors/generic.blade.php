@extends('layouts.app')

@section('title', 'Error')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i>Error</h3>
                </div>
                <div class="card-body text-center">
                    <div class="my-5">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        <h4 class="mb-4">{{ $message ?? 'Ha ocurrido un error' }}</h4>
                        
                        <p class="text-muted">Si el problema persiste, contacta al administrador del sistema.</p>
                        
                        <div class="mt-5">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-2"></i>Volver al Dashboard
                            </a>
                            
                            <button onclick="window.history.back()" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 