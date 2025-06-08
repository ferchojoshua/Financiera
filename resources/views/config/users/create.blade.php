@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Crear Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger border-left-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('config.users.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label font-weight-bold">Apellido</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label font-weight-bold">Nombre de usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label font-weight-bold">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password', 'toggle-password')">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label font-weight-bold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('password_confirmation', 'toggle-confirmation')">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label font-weight-bold">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->slug }}" {{ old('role') == $role->slug ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label font-weight-bold">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label font-weight-bold">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Guardar Usuario
                                </button>
                                <a href="{{ route('config.users.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left mr-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePass(fieldId, buttonId) {
    var field = document.getElementById(fieldId);
    var button = document.getElementById(buttonId);
    
    if (field.type === 'password') {
        field.type = 'text';
        button.querySelector('i').classList.remove('fa-eye');
        button.querySelector('i').classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.querySelector('i').classList.remove('fa-eye-slash');
        button.querySelector('i').classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>
@endpush
@endsection 