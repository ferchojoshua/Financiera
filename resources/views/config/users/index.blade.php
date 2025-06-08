@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Gestión de Usuarios</h4>
                </div>
                <div class="card-body">
                    <!-- Botón para agregar usuario -->
                    <div class="mb-3">
                        <a href="{{ route('config.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </a>
                    </div>

                    <!-- Filtros de búsqueda -->
                    <div class="filtros-de-busqueda">
                        <div>
                            <label for="buscar">Buscar</label>
                            <input type="text" id="buscar" name="buscar" placeholder="Nombre, Email, etc." class="form-control">
                        </div>
                        <div>
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol" class="form-control">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="botones">
                            <button type="button" class="filtro-btn" id="filtrar">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                            <button type="button" class="limpiar-btn" id="limpiar">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de usuarios -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->isNotEmpty())
                                            {{ $user->roles->first()->name }}
                                        @else
                                            Sin rol
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->phone ?? 'No disponible' }}</td>
                                    <td class="action-buttons">
                                        <a href="{{ route('config.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Función para filtrar usuarios
        function filtrarUsuarios() {
            var buscar = $('#buscar').val().toLowerCase();
            var rol = $('#rol').val().toLowerCase();
            var estado = $('#estado').val().toLowerCase();
            
            $('.table tbody tr').each(function() {
                var fila = $(this);
                var nombre = fila.find('td:nth-child(2)').text().toLowerCase();
                var email = fila.find('td:nth-child(3)').text().toLowerCase();
                var rolUsuario = fila.find('td:nth-child(4)').text().toLowerCase();
                var estadoUsuario = fila.find('td:nth-child(5)').text().toLowerCase();
                
                var mostrar = true;
                
                if (buscar && !(nombre.includes(buscar) || email.includes(buscar))) {
                    mostrar = false;
                }
                
                if (rol && !rolUsuario.includes(rol)) {
                    mostrar = false;
                }
                
                if (estado && !estadoUsuario.includes(estado)) {
                    mostrar = false;
                }
                
                fila.toggle(mostrar);
            });
        }
        
        // Eventos de botones
        $('#filtrar').click(filtrarUsuarios);
        
        $('#limpiar').click(function() {
            $('#buscar').val('');
            $('#rol').val('');
            $('#estado').val('');
            $('.table tbody tr').show();
        });
        
        // Filtrar también al presionar Enter
        $('#buscar, #rol, #estado').keypress(function(e) {
            if (e.which == 13) {
                filtrarUsuarios();
            }
        });
    });
    
    // Función para confirmar eliminación
    function confirmDelete(userId) {
        if (confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
            // Crear formulario para enviar la solicitud DELETE
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/config/users/' + userId;
            form.style.display = 'none';
            
            var tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = document.querySelector('meta[name="csrf-token"]').content;
            
            var methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(tokenField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            
            form.submit();
        }
    }
</script>
@endsection 