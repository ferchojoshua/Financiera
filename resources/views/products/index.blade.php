@extends('layouts.app')

@section('title', 'Catálogo de Productos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Catálogo de Productos</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Tasa de Interés</th>
                                    <th>Monto Mínimo</th>
                                    <th>Monto Máximo</th>
                                    <th>Plazo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->interest_rate }}%</td>
                                        <td>${{ number_format($product->min_amount, 2) }}</td>
                                        <td>${{ number_format($product->max_amount, 2) }}</td>
                                        <td>{{ $product->term }} días</td>
                                        <td>
                                            <span class="badge badge-{{ $product->status == 'active' ? 'success' : 'danger' }}">
                                                {{ $product->status == 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No hay productos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 