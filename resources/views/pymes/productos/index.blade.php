@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Productos Financieros</h4>
                    <a href="{{ route('pymes.productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Producto
                    </a>
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
                                    <th>Nombre</th>
                                    <th>Tasa de Interés</th>
                                    <th>Plazo Máximo (meses)</th>
                                    <th>Monto Mínimo</th>
                                    <th>Monto Máximo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Lista de productos financieros -->
                                <tr>
                                    <td>1</td>
                                    <td>Préstamo PYME Estándar</td>
                                    <td>12.5%</td>
                                    <td>36</td>
                                    <td>€ 5,000</td>
                                    <td>€ 50,000</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>
                                        <a href="{{ route('pymes.productos.show', 1) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Crédito Expansión</td>
                                    <td>10.0%</td>
                                    <td>48</td>
                                    <td>€ 25,000</td>
                                    <td>€ 150,000</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>
                                        <a href="{{ route('pymes.productos.show', 2) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Microcrédito</td>
                                    <td>15.0%</td>
                                    <td>24</td>
                                    <td>€ 1,000</td>
                                    <td>€ 10,000</td>
                                    <td><span class="badge bg-success">Activo</span></td>
                                    <td>
                                        <a href="{{ route('pymes.productos.show', 3) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Línea de Crédito Flexible</td>
                                    <td>14.0%</td>
                                    <td>60</td>
                                    <td>€ 10,000</td>
                                    <td>€ 100,000</td>
                                    <td><span class="badge bg-secondary">Inactivo</span></td>
                                    <td>
                                        <a href="{{ route('pymes.productos.show', 4) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 