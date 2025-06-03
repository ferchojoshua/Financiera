@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Preferencias del Sistema</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('config.preferences.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="company_name">Nombre de la Empresa</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" 
                                           value="{{ old('company_name', $preferences->company_name ?? '') }}">
                                    @error('company_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="logo">Logo de la Empresa</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo">
                                    @error('logo')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    @if(isset($preferences->logo_path) && $preferences->logo_path)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $preferences->logo_path) }}" 
                                                 alt="Logo actual" style="max-height: 100px;">
                                        </div>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label for="theme_color">Color del Tema</label>
                                    <input type="color" class="form-control @error('theme_color') is-invalid @enderror" 
                                           id="theme_color" name="theme_color" 
                                           value="{{ old('theme_color', $preferences->theme_color ?? '#00BFA5') }}">
                                    @error('theme_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="default_language">Idioma Predeterminado</label>
                                    <select class="form-select @error('default_language') is-invalid @enderror" 
                                            id="default_language" name="default_language">
                                        <option value="es" {{ (old('default_language', $preferences->default_language ?? '') == 'es') ? 'selected' : '' }}>Español</option>
                                        <option value="en" {{ (old('default_language', $preferences->default_language ?? '') == 'en') ? 'selected' : '' }}>Inglés</option>
                                    </select>
                                    @error('default_language')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="default_currency">Moneda Predeterminada</label>
                                    <select class="form-select @error('default_currency') is-invalid @enderror" 
                                            id="default_currency" name="default_currency">
                                        <option value="NIO" {{ (old('default_currency', $preferences->default_currency ?? '') == 'COR') ? 'selected' : '' }}>Cordobas (C$)</option>
                                        <option value="USD" {{ (old('default_currency', $preferences->default_currency ?? '') == 'USD') ? 'selected' : '' }}>Dólares ($)</option>
                                        <option value="EUR" {{ (old('default_currency', $preferences->default_currency ?? '') == 'EUR') ? 'selected' : '' }}>Euros (€)</option>
                                    </select>
                                    @error('default_currency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3">Notificaciones</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="email_notifications" 
                                           {{ old('email_notifications', $preferences->email_notifications ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        Habilitar notificaciones por correo electrónico
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" 
                                           name="sms_notifications" 
                                           {{ old('sms_notifications', $preferences->sms_notifications ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_notifications">
                                        Habilitar notificaciones por SMS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="mb-3">Configuración Avanzada</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="enable_customer_portal" 
                                           name="enable_customer_portal" 
                                           {{ old('enable_customer_portal', $preferences->enable_customer_portal ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enable_customer_portal">
                                        Habilitar portal de clientes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" 
                                           name="maintenance_mode" 
                                           {{ old('maintenance_mode', $preferences->maintenance_mode ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Activar modo de mantenimiento
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('config.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fa fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Guardar Preferencias
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 