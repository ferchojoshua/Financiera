@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Garantías</h4>
                    <a href="{{ route('pymes.garantias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Garantía
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
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Crédito</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($garantias) > 0)
                                    @foreach($garantias as $garantia)
                                    <tr>
                                        <td>{{ $garantia->id }}</td>
                                        <td>{{ $garantia->user->business_name }}</td>
                                        <td>{{ ucfirst($garantia->type) }}</td>
                                        <td>€ {{ number_format($garantia->value, 2, ',', '.') }}</td>
                                        <td>
                                            @if($garantia->status == 'active')
                                                <span class="badge bg-success">Activa</span>
                                            @elseif($garantia->status == 'pending')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($garantia->status == 'executed')
                                                <span class="badge bg-danger">Ejecutada</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $garantia->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($garantia->credit)
                                                #{{ $garantia->credit->id }}
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>{{ $garantia->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('pymes.garantias.show', $garantia->id) }}" class="btn btn-sm btn-info">
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
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center">No hay garantías registradas</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $garantias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 