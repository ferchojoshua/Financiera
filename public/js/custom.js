// Scripts personalizados para el sistema de préstamos

// Configuración global al cargar el documento
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes de Material Dashboard
    initializeMaterialDashboard();
    
    // Configurar campos de formulario especiales
    setupSpecialFormFields();
    
    // Inicializar notificaciones
    setupNotifications();
});

// Configuración de Material Dashboard
function initializeMaterialDashboard() {
    // Verificar si existe el objeto materialKit
    if (typeof materialKit !== 'undefined') {
        materialKit.initFormExtendedDatetimepickers();
        
        // Inicializar selectpicker si existe
        if (document.getElementsByClassName('selectpicker').length > 0) {
            $('.selectpicker').selectpicker();
        }
    }
    
    // Inicializar tooltips y popovers si existe Bootstrap
    if (typeof $ !== 'undefined' && typeof $.fn.tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
}

// Configuración de campos de formulario especiales
function setupSpecialFormFields() {
    // Máscara para campos monetarios
    const moneyInputs = document.querySelectorAll('.money-input');
    if (moneyInputs.length > 0) {
        moneyInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9.]/g, '');
                if(value) {
                    // Formatear a 2 decimales
                    value = parseFloat(value).toFixed(2);
                    e.target.value = value;
                }
            });
        });
    }
    
    // Campos de fecha
    const dateInputs = document.querySelectorAll('.date-input');
    if (dateInputs.length > 0 && typeof $ !== 'undefined' && typeof $.fn.datetimepicker === 'function') {
        $('.date-input').datetimepicker({
            format: 'YYYY-MM-DD',
            icons: {
                time: "fa fa-clock",
                date: "fa fa-calendar",
                up: "fa fa-chevron-up",
                down: "fa fa-chevron-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-screenshot",
                clear: "fa fa-trash",
                close: "fa fa-remove"
            }
        });
    }
}

// Configuración de notificaciones
function setupNotifications() {
    // Auto-ocultar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert-dismissible:not(.persistent)');
    if (alerts.length > 0) {
        alerts.forEach(alert => {
            setTimeout(() => {
                // Buscar el botón close y simular click
                const closeBtn = alert.querySelector('.close');
                if (closeBtn) {
                    closeBtn.click();
                } else {
                    // Alternativa: agregar clase para desvanecer
                    alert.classList.add('fade');
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }
            }, 5000);
        });
    }
    
    // Mostrar notificación usando Bootstrap Notify si está disponible
    window.showNotification = function(message, type = 'info', icon = null) {
        if (typeof $ !== 'undefined' && typeof $.notify === 'function') {
            const icons = {
                'info': 'notifications',
                'success': 'check_circle',
                'warning': 'warning',
                'danger': 'error'
            };
            
            $.notify({
                icon: icon || icons[type] || 'notifications',
                message: message
            }, {
                type: type,
                timer: 3000,
                placement: {
                    from: 'top',
                    align: 'right'
                }
            });
        } else {
            // Fallback a alert si no está disponible notify
            alert(message);
        }
    };
}

// Función para confirmar acciones
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Función para formatear moneda
function formatMoney(amount, currency = '$') {
    return currency + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Función para manejar errores de peticiones AJAX
function handleAjaxError(xhr, status, error) {
    console.error('Error en petición AJAX:', status, error);
    
    let errorMessage = 'Ha ocurrido un error en la petición';
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
    } else if (xhr.responseText) {
        try {
            const response = JSON.parse(xhr.responseText);
            errorMessage = response.message || errorMessage;
        } catch (e) {
            // Si no es JSON, usar el mensaje de error general
        }
    }
    
    window.showNotification(errorMessage, 'danger');
}

// Limpieza de formularios
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        
        // Resetear selectpickers
        if (typeof $ !== 'undefined' && typeof $.fn.selectpicker === 'function') {
            $(form).find('.selectpicker').selectpicker('refresh');
        }
        
        // Limpiar errores de validación
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        form.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.innerHTML = '';
        });
    }
} 