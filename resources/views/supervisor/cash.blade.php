@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestión de Caja</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Saldo Total</h5>
                                    <h3 class="card-text">$ {{ number_format($saldoTotal ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Ingresos Hoy</h5>
                                    <h3 class="card-text">$ {{ number_format($ingresosHoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Egresos Hoy</h5>
                                    <h3 class="card-text">$ {{ number_format($egresosHoy ?? 0, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Registrar Ingreso</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('supervisor.cash.income') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="ingreso">
                                        <div class="form-group">
                                            <label for="amount">Monto</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Categoría</label>
                                            <select class="form-control" id="category_id" name="category_id">
                                                <option value="">Seleccione una categoría</option>
                                                @if(isset($categories) && count($categories) > 0)
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Descripción</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fa fa-plus-circle"></i> Registrar Ingreso
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">Registrar Egreso</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('supervisor.cash.expense') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="egreso">
                                        <div class="form-group">
                                            <label for="amount">Monto</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="category_id">Categoría</label>
                                            <select class="form-control" id="category_id" name="category_id">
                                                <option value="">Seleccione una categoría</option>
                                                @if(isset($categories) && count($categories) > 0)
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Descripción</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fa fa-minus-circle"></i> Registrar Egreso
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Últimos Movimientos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Categoría</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($movimientos) && count($movimientos) > 0)
                                            @foreach($movimientos as $movimiento)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        @if($movimiento->type == 'ingreso')
                                                            <span class="badge badge-success">Ingreso</span>
                                                        @else
                                                            <span class="badge badge-danger">Egreso</span>
                                                        @endif
                                                    </td>
                                                    <td>$ {{ number_format($movimiento->amount, 2) }}</td>
                                                    <td>{{ $movimiento->category ? $movimiento->category->name : 'Sin categoría' }}</td>
                                                    <td>{{ $movimiento->description }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No hay movimientos registrados</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 