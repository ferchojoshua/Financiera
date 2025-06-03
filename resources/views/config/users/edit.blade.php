@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Editar Usuario</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('config.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">Apellidos</label>
                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Contraseña</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                <small class="form-text text-muted">Dejar en blanco para mantener la misma contraseña.</small>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirmar Contraseña</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">Teléfono</label>
                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="address" class="col-md-4 col-form-label text-md-right">Dirección</label>
                            <div class="col-md-6">
                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" name="address">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-right">Rol</label>
                            <div class="col-md-6">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Cliente</option>
                                    <option value="colector" {{ old('role', $user->role) == 'colector' ? 'selected' : '' }}>Colector</option>
                                    <option value="caja" {{ old('role', $user->role) == 'caja' ? 'selected' : '' }}>Cajero</option>
                                    <option value="supervisor" {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    @if(auth()->user()->isSuperAdmin())
                                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Administrador</option>
                                    @endif
                                </select>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Estado</label>
                            <div class="col-md-6">
                                <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Campos específicos según el rol -->
                        <div id="fields-colector" class="role-fields" style="display: {{ old('role', $user->role) == 'colector' ? 'block' : 'none' }}">
                            <div class="form-group row mb-3">
                                <label for="nit" class="col-md-4 col-form-label text-md-right">NIT</label>
                                <div class="col-md-6">
                                    <input id="nit" type="text" class="form-control @error('nit') is-invalid @enderror" name="nit" value="{{ old('nit', $user->nit) }}">
                                    @error('nit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="zone" class="col-md-4 col-form-label text-md-right">Zona</label>
                                <div class="col-md-6">
                                    <input id="zone" type="text" class="form-control @error('zone') is-invalid @enderror" name="zone" value="{{ old('zone', $user->zone) }}">
                                    @error('zone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="fields-user" class="role-fields" style="display: {{ old('role', $user->role) == 'user' ? 'block' : 'none' }}">
                            <div class="form-group row mb-3">
                                <label for="business_name" class="col-md-4 col-form-label text-md-right">Nombre de Empresa</label>
                                <div class="col-md-6">
                                    <input id="business_name" type="text" class="form-control @error('business_name') is-invalid @enderror" name="business_name" value="{{ old('business_name', $user->business_name) }}">
                                    @error('business_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="tax_id" class="col-md-4 col-form-label text-md-right">Cedula</label>
                                <div class="col-md-6">
                                    <input id="tax_id" type="text" class="form-control @error('tax_id') is-invalid @enderror" name="tax_id" value="{{ old('tax_id', $user->tax_id) }}">
                                    @error('tax_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="business_sector" class="col-md-4 col-form-label text-md-right">Sector Empresarial</label>
                                <div class="col-md-6">
                                    <select id="business_sector" class="form-control @error('business_sector') is-invalid @enderror" name="business_sector">
                                        <option value="">Seleccionar...</option>
                                        <option value="Agricultura" {{ old('business_sector', $user->business_sector) == 'Agricultura' ? 'selected' : '' }}>Agricultura</option>
                                        <option value="Comercio" {{ old('business_sector', $user->business_sector) == 'Comercio' ? 'selected' : '' }}>Comercio</option>
                                        <option value="Construcción" {{ old('business_sector', $user->business_sector) == 'Construcción' ? 'selected' : '' }}>Construcción</option>
                                        <option value="Educación" {{ old('business_sector', $user->business_sector) == 'Educación' ? 'selected' : '' }}>Educación</option>
                                        <option value="Hostelería" {{ old('business_sector', $user->business_sector) == 'Hostelería' ? 'selected' : '' }}>Hostelería</option>
                                        <option value="Industria" {{ old('business_sector', $user->business_sector) == 'Industria' ? 'selected' : '' }}>Industria</option>
                                        <option value="Salud" {{ old('business_sector', $user->business_sector) == 'Salud' ? 'selected' : '' }}>Salud</option>
                                        <option value="Servicios" {{ old('business_sector', $user->business_sector) == 'Servicios' ? 'selected' : '' }}>Servicios</option>
                                        <option value="Tecnología" {{ old('business_sector', $user->business_sector) == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                                        <option value="Transporte" {{ old('business_sector', $user->business_sector) == 'Transporte' ? 'selected' : '' }}>Transporte</option>
                                        <option value="Otros" {{ old('business_sector', $user->business_sector) == 'Otros' ? 'selected' : '' }}>Otros</option>
                                    </select>
                                    @error('business_sector')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="economic_activity" class="col-md-4 col-form-label text-md-right">Actividad Económica</label>
                                <div class="col-md-6">
                                    <input id="economic_activity" type="text" class="form-control @error('economic_activity') is-invalid @enderror" name="economic_activity" value="{{ old('economic_activity', $user->economic_activity) }}">
                                    @error('economic_activity')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('config.users.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const fieldsColector = document.getElementById('fields-colector');
        const fieldsUser = document.getElementById('fields-user');
        
        roleSelect.addEventListener('change', function() {
            if (this.value === 'colector') {
                fieldsColector.style.display = 'block';
                fieldsUser.style.display = 'none';
            } else if (this.value === 'user') {
                fieldsColector.style.display = 'none';
                fieldsUser.style.display = 'block';
            } else {
                fieldsColector.style.display = 'none';
                fieldsUser.style.display = 'none';
            }
        });
    });
</script>
@endsection 