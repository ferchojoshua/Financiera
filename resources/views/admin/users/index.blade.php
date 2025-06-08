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
</script> 