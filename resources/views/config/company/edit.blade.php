@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Configuración de la Empresa</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('config.company.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nombre de la Empresa *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $company->name ?? old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="ruc" class="form-label">RUC / Número de Identificación Fiscal *</label>
                                    <input type="text" class="form-control" id="ruc" name="ruc" value="{{ $company->ruc ?? old('ruc') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Dirección</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ $company->address ?? old('address') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $company->phone ?? old('phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $company->email ?? old('email') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="website" class="form-label">Sitio Web</label>
                                    <input type="text" class="form-control" id="website" name="website" value="{{ $company->website ?? old('website') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="legal_representative" class="form-label">Representante Legal</label>
                                    <input type="text" class="form-control" id="legal_representative" name="legal_representative" value="{{ $company->legal_representative ?? old('legal_representative') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="logo" class="form-label">Logo de la Empresa</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            
                            @if(isset($company) && $company->logo_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo de la empresa" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>

                        <h5 class="border-bottom pb-2 mt-4">Información para Documentos</h5>

                        <div class="form-group mb-3">
                            <label for="receipt_message" class="form-label">Mensaje en Recibos</label>
                            <textarea class="form-control" id="receipt_message" name="receipt_message" rows="2">{{ $company->receipt_message ?? old('receipt_message') }}</textarea>
                            <small class="form-text text-muted">Este texto aparecerá en los recibos de pago.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="contract_footer" class="form-label">Pie de Página para Contratos</label>
                            <textarea class="form-control" id="contract_footer" name="contract_footer" rows="2">{{ $company->contract_footer ?? old('contract_footer') }}</textarea>
                            <small class="form-text text-muted">Este texto aparecerá al final de los contratos.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="payment_terms" class="form-label">Términos de Pago</label>
                            <textarea class="form-control" id="payment_terms" name="payment_terms" rows="3">{{ $company->payment_terms ?? old('payment_terms') }}</textarea>
                            <small class="form-text text-muted">Información sobre términos y condiciones de pago.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="footer_text" class="form-label">Pie de Página General</label>
                            <textarea class="form-control" id="footer_text" name="footer_text" rows="2">{{ $company->footer_text ?? old('footer_text') }}</textarea>
                            <small class="form-text text-muted">Este texto aparecerá en todos los documentos emitidos.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 