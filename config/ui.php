<?php

return [
    // Configuración general de la interfaz de usuario
    'layout' => 'layouts.master', // Diseño predeterminado del sistema
    
    // Configuración para evitar conflictos con el sistema antiguo
    'force_new_layout' => true, // Forzar el uso del nuevo layout en todo el sistema
    
    // Mapeo de rutas a layouts específicos
    'route_layouts' => [
        'clients.*' => 'layouts.master',
        'client.*' => 'layouts.app',
    ],
]; 