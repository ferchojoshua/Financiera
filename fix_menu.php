<?php
// Este archivo tiene instrucciones para corregir el menú

/*
Realizar los siguientes cambios en resources/views/layouts/app.blade.php:

1. Eliminar la sección duplicada de menú para el usuario superadmin que comienza después de:
@endif
@endif

Y termina antes de:
<a href="{{ route('logout') }}" class="menu-item" ...

2. Restaurar la sección de menús para roles regulares que comienza con:
<!-- Menús para roles regulares (supervisor, agente, etc.) -->

3. La estructura corregida debería ser:

@endif
@endif

<!-- Menús para roles regulares (supervisor, agente, etc.) -->
                
<!-- Clientes -->
<div class="menu-dropdown" data-toggle="clientes-regular">
    <div class="menu-dropdown-icon">
        <i class="fa fa-users"></i> Clientes
    </div>
    <i class="fa fa-chevron-down"></i>
</div>
...
... (resto del código del menú regular)
...

<a href="{{ route('logout') }}" class="menu-item" ...

*/

echo "Por favor, edite manualmente el archivo resources/views/layouts/app.blade.php siguiendo las instrucciones anteriores.";
?> 