@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalle de Gastos</h4>
                    <a href="{{ route('contabilidad.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Categoría/Tipo</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastos as $gasto)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($gasto->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $gasto->description ?? 'Gasto' }}</td>
                                        <td>${{ number_format($gasto->amount, 2) }}</td>
                                        <td>{{ $gasto->category_name ?? $gasto->type ?? 'General' }}</td>
                                        <td>{{ $gasto->user_name ?? $gasto->id_agent ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay gastos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $gastos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 