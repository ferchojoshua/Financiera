@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editar Billetera</h5>
                    <div>
                        <a href="{{ route('admin.wallet.show', $wallet->id) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> Ver Detalles
                        </a>
                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-arrow-left"></i> Volver a la lista
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <strong>Información:</strong> La edición solo permite modificar la descripción de la billetera. Para gestionar el saldo, utilice las opciones de depósito y retiro en la página de detalles.
                    </div>

                    <form method="POST" action="{{ route('admin.wallet.update', $wallet->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $wallet->id }}</p>
                                <p><strong>Usuario:</strong> {{ $wallet->user->name }}</p>
                                <p><strong>Email:</strong> {{ $wallet->user->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Saldo Actual:</strong> ${{ number_format($wallet->balance, 2) }}</p>
                                <p><strong>Fecha de Creación:</strong> {{ $wallet->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Última Actualización:</strong> {{ $wallet->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $wallet->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Puede añadir una descripción o notas sobre esta billetera.</small>
                        </div>

                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 