@extends('layouts.app')

@section('title', 'Detalles de Entrada Contable')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles de Entrada Contable</h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h5>Información General</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px;">Fecha:</th>
                                            <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipo:</th>
                                            <td>
                                                @switch($entry->entry_type)
                                                    @case('ingreso')
                                                        <span class="badge badge-success">Ingreso</span>
                                                        @break
                                                    @case('gasto')
                                                        <span class="badge badge-danger">Gasto</span>
                                                        @break
                                                    @case('ajuste')
                                                        <span class="badge badge-warning">Ajuste</span>
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Monto:</th>
                                            <td>$ {{ number_format($entry->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Categoría:</th>
                                            <td>{{ $entry->category }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h5>Información Adicional</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 150px;">Referencia:</th>
                                            <td>{{ $entry->reference ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cuenta Contable:</th>
                                            <td>{{ $entry->accounting_account ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Creado por:</th>
                                            <td>{{ $entry->user ? $entry->user->name : 'Sistema' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Creación:</th>
                                            <td>{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <h5>Descripción</h5>
                                    <p>{{ $entry->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($entry->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="info-box">
                                    <div class="info-box-content">
                                        <h5>Notas</h5>
                                        <p>{{ $entry->notes }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('accounting.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('accounting.edit', $entry->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('accounting.destroy', $entry->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('¿Está seguro de eliminar esta entrada?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 