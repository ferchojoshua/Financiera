/**
 * Selector de Funciones Interactivo
 * 
 * Este script permite agregar iconos interactivos para seleccionar funciones
 * en diferentes secciones del sistema.
 */
class FunctionSelector {
    /**
     * Constructor
     * 
     * @param {string} selector - Selector CSS del contenedor donde se renderizarán los iconos
     * @param {Object} options - Opciones de configuración
     */
    constructor(selector, options = {}) {
        this.container = document.querySelector(selector);
        this.options = Object.assign({
            iconSize: 'lg',
            activeClass: 'active-function',
            animationClass: 'pulse',
            onSelect: null,
            theme: 'light'
        }, options);
        
        this.functions = [];
        this.selectedFunction = null;
    }
    
    /**
     * Añadir una función al selector
     * 
     * @param {string} id - Identificador único de la función
     * @param {string} name - Nombre de la función
     * @param {string} icon - Clase del icono FontAwesome
     * @param {string} description - Descripción corta de la función
     * @param {string} action - URL o acción a ejecutar al seleccionar
     */
    addFunction(id, name, icon, description, action) {
        this.functions.push({
            id,
            name,
            icon,
            description,
            action
        });
        
        return this;
    }
    
    /**
     * Renderizar todos los iconos de funciones en el contenedor
     */
    render() {
        if (!this.container) {
            console.error('Contenedor no encontrado');
            return;
        }
        
        // Aplicar tema
        this.container.classList.add(`function-selector-${this.options.theme}`);
        
        // Crear elementos
        const functionGrid = document.createElement('div');
        functionGrid.className = 'function-selector-grid';
        
        this.functions.forEach(func => {
            const functionItem = document.createElement('div');
            functionItem.className = 'function-item';
            functionItem.dataset.id = func.id;
            
            const iconEl = document.createElement('div');
            iconEl.className = 'function-icon';
            iconEl.innerHTML = `<i class="fas ${func.icon} fa-${this.options.iconSize}"></i>`;
            
            const nameEl = document.createElement('div');
            nameEl.className = 'function-name';
            nameEl.textContent = func.name;
            
            const descEl = document.createElement('div');
            descEl.className = 'function-description';
            descEl.textContent = func.description;
            
            functionItem.appendChild(iconEl);
            functionItem.appendChild(nameEl);
            functionItem.appendChild(descEl);
            
            // Manejar selección
            functionItem.addEventListener('click', () => this.selectFunction(func));
            
            functionGrid.appendChild(functionItem);
        });
        
        this.container.appendChild(functionGrid);
        
        // Añadir estilos si no existen
        if (!document.getElementById('function-selector-styles')) {
            this.addStyles();
        }
    }
    
    /**
     * Seleccionar una función
     * 
     * @param {Object} func - La función seleccionada
     */
    selectFunction(func) {
        // Eliminar selección anterior
        const previousSelected = this.container.querySelector(`.${this.options.activeClass}`);
        if (previousSelected) {
            previousSelected.classList.remove(this.options.activeClass);
        }
        
        // Marcar nueva selección
        const selectedEl = this.container.querySelector(`[data-id="${func.id}"]`);
        if (selectedEl) {
            selectedEl.classList.add(this.options.activeClass);
            
            // Aplicar animación
            selectedEl.classList.add(this.options.animationClass);
            setTimeout(() => {
                selectedEl.classList.remove(this.options.animationClass);
            }, 700);
        }
        
        this.selectedFunction = func;
        
        // Ejecutar callback si existe
        if (typeof this.options.onSelect === 'function') {
            this.options.onSelect(func);
        }
        
        // Si es una URL, navegar a ella
        if (func.action && func.action.startsWith('http')) {
            window.location.href = func.action;
        }
    }
    
    /**
     * Agregar estilos CSS necesarios
     */
    addStyles() {
        const styleEl = document.createElement('style');
        styleEl.id = 'function-selector-styles';
        styleEl.textContent = `
            .function-selector-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
                margin: 1rem 0;
            }
            
            .function-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 1rem;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
            }
            
            .function-selector-light .function-item {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
            }
            
            .function-selector-dark .function-item {
                background-color: #343a40;
                border: 1px solid #495057;
                color: #f8f9fa;
            }
            
            .function-icon {
                margin-bottom: 0.5rem;
                color: #007bff;
            }
            
            .function-name {
                font-weight: bold;
                margin-bottom: 0.25rem;
            }
            
            .function-description {
                font-size: 0.8rem;
                color: #6c757d;
            }
            
            .function-selector-dark .function-description {
                color: #adb5bd;
            }
            
            .function-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .function-selector-light .function-item:hover {
                background-color: #e9ecef;
            }
            
            .function-selector-dark .function-item:hover {
                background-color: #495057;
            }
            
            .function-item.active-function {
                border-color: #007bff;
                background-color: rgba(0, 123, 255, 0.1);
            }
            
            .function-selector-dark .function-item.active-function {
                border-color: #007bff;
                background-color: rgba(0, 123, 255, 0.2);
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .pulse {
                animation: pulse 0.7s ease;
            }
        `;
        
        document.head.appendChild(styleEl);
    }
}

// Exponer globalmente
window.FunctionSelector = FunctionSelector; 