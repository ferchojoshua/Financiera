@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestión de Billeteras</h5>
                    <a href="{{ route('admin.wallet.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Nueva Billetera
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(count($wallets) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Saldo Actual</th>
                                        <th>Descripción</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wallets as $wallet)
                                        <tr>
                                            <td>{{ $wallet->id }}</td>
                                            <td>{{ $wallet->user->name }}</td>
                                            <td>${{ number_format($wallet->balance, 2) }}</td>
                                            <td>{{ $wallet->description ?? 'N/A' }}</td>
                                            <td>{{ $wallet->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.wallet.edit', $wallet->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="event.preventDefault();
                                                                    if(confirm('¿Está seguro de eliminar esta billetera?')) {
                                                                        document.getElementById('delete-form-{{ $wallet->id }}').submit();
                                                                    }" title="Eliminar">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $wallet->id }}" action="{{ route('admin.wallet.destroy', $wallet->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay billeteras registradas. Puede crear una nueva billetera haciendo clic en el botón "Nueva Billetera".
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 