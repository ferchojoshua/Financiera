{{-- 
    Componente Selector de Funciones
    
    Parámetros:
    - id: ID único para el contenedor (obligatorio)
    - theme: Tema a utilizar (light/dark) (opcional, default: light)
    - functions: Arreglo de funciones en formato JSON (obligatorio)
    - iconSize: Tamaño de los iconos (opcional, default: 2x)
    - onSelectCallback: Función JavaScript a ejecutar cuando se selecciona una función (opcional)
--}}

@props(['id', 'theme' => 'light', 'functions', 'iconSize' => '2x', 'onSelectCallback' => null])

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'function-selector']) }}></div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el selector de funciones
        const selector = new FunctionSelector('#{{ $id }}', {
            theme: '{{ $theme }}',
            iconSize: '{{ $iconSize }}',
            onSelect: {{ $onSelectCallback ? 'function(func) {' . $onSelectCallback . '}' : 'null' }}
        });
        
        // Agregar las funciones desde el JSON
        const functions = {!! $functions !!};
        
        functions.forEach(func => {
            selector.addFunction(
                func.id, 
                func.name, 
                func.icon, 
                func.description, 
                func.action
            );
        });
        
        // Renderizar
        selector.render();
    });
</script>
@endpush 