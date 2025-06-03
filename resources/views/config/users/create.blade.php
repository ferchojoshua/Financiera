@extends('layouts.app')

@section('content')
<script>
// Definir togglePass como una función global
window.togglePass = function(fieldId, buttonId) {
    var field = document.getElementById(fieldId);
    var button = document.getElementById(buttonId);
    var icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Definir generarPassword como una función global
window.generarPassword = function() {
    // Generar contraseña aleatoria
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
    var randomPassword = '';
    for (var i = 0; i < 10; i++) {
        randomPassword += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    // Asignar la contraseña a ambos campos
    var passwordField = document.getElementById('password');
    var confirmField = document.getElementById('password_confirmation');
    
    passwordField.value = randomPassword;
    confirmField.value = randomPassword;
    
    // Mostrar contraseña en texto plano
    passwordField.type = 'text';
    confirmField.type = 'text';
    
    // Actualizar íconos
    document.querySelector('#toggle-password i').classList.remove('fa-eye');
    document.querySelector('#toggle-password i').classList.add('fa-eye-slash');
    
    document.querySelector('#toggle-confirmation i').classList.remove('fa-eye');
    document.querySelector('#toggle-confirmation i').classList.add('fa-eye-slash');
    
    return false; // Prevenir comportamiento predeterminado
}
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Añadir Miembro</h4>
                    <button type="button" class="close" aria-label="Close" onclick="window.history.back()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('config.users.store') }}" method="POST">
                        @csrf
                        
                        <h5 class="mb-3">Información General</h5>
                        

                        <div class="form-group mb-3">
                            <label for="name">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre" value="{{ old('name') }}" required tabindex="1">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="last_name">Apellido</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Apellido" value="{{ old('last_name') }}" tabindex="2">
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Teléfono</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Teléfono" value="{{ old('phone') }}" tabindex="3">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label>Género</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="M" {{ old('gender') == 'M' ? 'checked' : '' }} checked>
                                    <label class="form-check-label" for="gender_male">Hombre</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="F" {{ old('gender') == 'F' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gender_female">Mujer</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <h5 class="mb-3">Configuración de Cuenta</h5>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required tabindex="4">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="username">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" value="{{ old('username') }}" required tabindex="5">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required tabindex="6">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="toggle-password" tabindex="8" onclick="togglePass('password', 'toggle-password')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar Contraseña" required tabindex="7">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="toggle-confirmation" tabindex="8" onclick="togglePass('password_confirmation', 'toggle-confirmation')">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required tabindex="9">
                                <option value="">Selecciona una opción</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}" {{ old('role') == $role->slug ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campo oculto para level con valor predeterminado -->
                        <input type="hidden" name="level" id="level" value="1">

                        <div class="text-right mt-4">
                            <a href="{{ route('config.users.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inicializar cuando el documento esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirmation = document.getElementById('password_confirmation').value;
            
            // Verificar que las contraseñas coincidan
            if (password !== confirmation) {
                alert('Las contraseñas no coinciden');
                document.getElementById('password_confirmation').focus();
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush

@section('styles')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .form-label {
        font-weight: 600;
    }
    .bg-light {
        background-color: #f8f9fa;
    }
</style>
@endsection
@endsection 