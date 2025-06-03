@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">{{ $title ?? 'Error en el Módulo de Pagos' }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <p>{{ $message ?? 'Ha ocurrido un error inesperado. Por favor, intenta más tarde o contacta al administrador.' }}</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ url('/home') }}" class="btn btn-primary">
                            <i class="fa fa-home"></i> Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 