{{-- Vista de prueba para diagnóstico --}}
@extends('layouts.master')

@section('title', 'Prueba Clientes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Listado de Clientes (PRUEBA)</h4>
                    <p class="card-category">Vista de prueba para diagnóstico</p>
                </div>
                <div class="card-body">
                    <p>Si estás viendo esto con el layout correcto (Material Design), el problema está en la vista original.</p>
                    <p>Si sigues viendo el menú lateral verde, el problema está en cómo Laravel carga los layouts.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 