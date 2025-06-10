@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-users"></i> Reporte de Clientes
                    </h4>
                </div>
                <div class="card-body">
                    <p>Este es el reporte general de clientes.</p>
                    <p><em>(Funcionalidad en desarrollo)</em></p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total de Clientes</h5>
                                    <p class="card-text display-4">{{ $totalClients }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Clientes Activos</h5>
                                    <p class="card-text display-4">{{ $activeClients }}</p>
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