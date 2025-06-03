@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>{{ __('Gestión de Billeteras') }}</h4>
                        <a href="{{ url('admin/wallet/create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> {{ __('Nueva Billetera') }}
                        </a>
                    </div>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Usuario') }}</th>
                                    <th>{{ __('Saldo Actual') }}</th>
                                    <th>{{ __('Descripción') }}</th>
                                    <th>{{ __('Fecha Creación') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wallets as $wallet)
                                    <tr>
                                        <td>{{ $wallet->id }}</td>
                                        <td>{{ $wallet->user->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($wallet->balance ?? 0, 2) }}</td>
                                        <td>{{ $wallet->description ?? 'Sin descripción' }}</td>
                                        <td>{{ $wallet->created_at ? $wallet->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('wallets.show', $wallet->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('wallets.edit', $wallet->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if(Auth::user()->isAdmin() || Auth::user()->role == 'superadmin')
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="event.preventDefault();
                                                                if(confirm('¿Está seguro de eliminar esta billetera?')) {
                                                                    document.getElementById('delete-form-{{ $wallet->id }}').submit();
                                                                }" title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $wallet->id }}" action="{{ route('wallets.destroy', $wallet->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-info m-0">
                                                {{ __('No hay billeteras registradas. Puede crear una nueva billetera haciendo clic en el botón "Nueva Billetera".') }}
                                            </div>
                                        </td>
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