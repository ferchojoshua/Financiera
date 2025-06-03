@extends('layouts.app')

@section('admin-section')
<!-- Esta sección se mostrará en la parte superior para superadmins -->
<div class="col-12 mb-4">
    <div class="card bg-light">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fa fa-user-plus"></i> Administración de Clientes
            </h5>
            <p class="card-text">
                Desde aquí puede crear nuevos clientes en el sistema. Complete todos los campos requeridos y proporcione 
                la información adicional que considere relevante para un mejor seguimiento.
            </p>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Verificador de carga de vista -->
<!-- <div id="vista-cargada" style="display:none">Vista client/create cargada correctamente</div> -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Añadir Rápido</h4>
                    <button type="button" class="btn-close text-white" aria-label="Close"></button>
                </div>
                <div class="card-body">
                    <!-- Mensajes de alerta -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{url('client')}}" class="new-register" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="client_type">Cliente</label>
                                <select id="client_type" name="client_type" class="form-select">
                                    <option value="">Cliente</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="branch_id">Sucursal</label>
                                <select id="branch_id" name="branch_id" class="form-select" required>
                                    <option value="">Seleccione sucursal</option>
                                    @if(isset($branches) && count($branches) > 0)
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="1">Sucursal Principal</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-2">Datos del cliente</h5>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Nombre</label>
                                    <input type="text" name="name" value="{{isset($user) ? $user->name : ''}}" class="form-control" id="name" placeholder="Nombres y Apellidos" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="route">Ruta</label>
                                    <select id="route" name="route" class="form-select">
                                        <option value="">Seleccione ruta</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nit_number">Cédula</label>
                                    <input type="text" name="nit_number" value="{{isset($user) ? $user->nit : ''}}" class="form-control" id="nit_number" placeholder="Cédula" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="address">Dirección</label>
                                    <input type="text" name="address" value="{{isset($user) ? $user->address : ''}}" class="form-control" id="address" placeholder="Dirección" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input type="tel" name="phone" value="{{isset($user) ? $user->phone : ''}}" class="form-control" id="phone" placeholder="Teléfono" required>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender">Género</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Seleccione</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="house">Casa</label>
                                    <select id="house" name="house" class="form-select">
                                        <option value="">Seleccione</option>
                                        <option value="propia">Propia</option>
                                        <option value="alquilada">Alquilada</option>
                                        <option value="familiar">Familiar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="civil_status">Estado civil</label>
                                    <select id="civil_status" name="civil_status" class="form-select">
                                        <option value="">Seleccione</option>
                                        <option value="casado">Casado</option>
                                        <option value="soltero">Soltero</option>
                                        <option value="union_libre">Unión Libre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="spouse_name">Cónyuge</label>
                                    <input type="text" name="spouse_name" class="form-control" id="spouse_name" placeholder="Nombre cónyuge">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="spouse_job">Oficio</label>
                                    <input type="text" name="spouse_job" class="form-control" id="spouse_job" placeholder="Oficio cónyuge">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="spouse_phone">Teléfono</label>
                                    <input type="tel" name="spouse_phone" class="form-control" id="spouse_phone" placeholder="Teléfono cónyuge">
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="border-bottom pb-2 mt-4">Datos del negocio</h5>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="business_type">Tipo</label>
                                    <input type="text" name="business_type" class="form-control" id="business_type" placeholder="Tipo de negocio">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="business_time">Tiempo</label>
                                    <input type="text" name="business_time" class="form-control" id="business_time" placeholder="Tiempo del negocio">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_good">Ventas buenas</label>
                                    <input type="text" name="sales_good" class="form-control" id="sales_good" placeholder="Ventas días buenos">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales_bad">Ventas malas</label>
                                    <input type="text" name="sales_bad" class="form-control" id="sales_bad" placeholder="Ventas días malos">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weekly_average">Promedio</label>
                                    <input type="text" name="weekly_average" class="form-control" id="weekly_average" placeholder="Promedio semanal">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="net_profit">Ganancias</label>
                                    <input type="text" name="net_profit" class="form-control" id="net_profit" placeholder="Ganancias netas">
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="border-bottom pb-2 mt-4">Datos del préstamo</h5>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="first_payment">Primer pago</label>
                                    <input type="date" name="first_payment" class="form-control" id="first_payment" value="2025-06-01">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="requested_amount">Monto solicitado</label>
                                    <input type="number" step="any" min="1" name="requested_amount" class="form-control" id="requested_amount" placeholder="Monto solicitado">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="approved_amount">Monto aprobado</label>
                                    <input type="number" step="any" min="1" name="approved_amount" class="form-control" id="approved_amount" placeholder="Monto aprobado">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="interest_rate">Interés</label>
                                    <input type="number" step="0.01" min="0" name="interest_rate" class="form-control" id="interest_rate" placeholder="Interés">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_type">Tipo de pago</label>
                                    <select id="payment_type" name="payment_type" class="form-select">
                                        <option value="diario">Diario</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="quincenal">Quincenal</option>
                                        <option value="mensual">Mensual</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="term">Plazo</label>
                                    <input type="number" min="1" name="term" class="form-control" id="term" placeholder="Plazo">
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="recovery">Recuperación</label>
                                    <input type="text" name="recovery" class="form-control" id="recovery" placeholder="Recuperación" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="benefit">Beneficio</label>
                                    <input type="text" name="benefit" class="form-control" id="benefit" placeholder="Beneficio" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="lat" value="{{isset($user) ? $user->lat : ''}}" class="form-control" id="lat">
                        <input type="hidden" name="lng" value="{{isset($user) ? $user->lng : ''}}" class="form-control" id="lng">
                        
                        <div class="row mt-4">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Guardar Cliente
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnCerrar">
                                    <i class="fa fa-times"></i> Cerrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Agregamos jQuery para asegurar que esté disponible -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Código para verificar si la vista se cargó
document.getElementById('vista-cargada').style.display = 'block';
console.log('Vista de cliente cargada correctamente');

// Esperar a que todo el documento esté listo
$(document).ready(function() {
    console.log('jQuery document ready - Formulario de clientes');
    
    // Mostrar mensaje para confirmar que la vista se cargó
   // alert('Vista de creación de clientes cargada. Presione OK para continuar.');
    
    // Verificar si los elementos existen
    console.log('Elemento requested_amount:', document.getElementById('requested_amount'));
    console.log('Elemento interest_rate:', document.getElementById('interest_rate'));
    console.log('Elemento term:', document.getElementById('term'));
    console.log('Elemento branch_id:', document.getElementById('branch_id'));
    
    // Calcular montos cuando cambian los valores
    if (document.getElementById('requested_amount')) {
        document.getElementById('requested_amount').addEventListener('input', function() {
            console.log('Evento input en requested_amount:', this.value);
            calculateAmounts();
        });
    }
    
    if (document.getElementById('interest_rate')) {
        document.getElementById('interest_rate').addEventListener('input', function() {
            console.log('Evento input en interest_rate:', this.value);
            calculateAmounts();
        });
    }
    
    if (document.getElementById('term')) {
        document.getElementById('term').addEventListener('input', function() {
            console.log('Evento input en term:', this.value);
            calculateAmounts();
        });
    }
    
    // Cambiar las rutas disponibles según la sucursal seleccionada
    if (document.getElementById('branch_id')) {
        document.getElementById('branch_id').addEventListener('change', function() {
            const branchId = this.value;
            console.log('Branch ID seleccionado:', branchId);
            
            if (branchId) {
                // Hacer una solicitud AJAX para obtener las rutas de la sucursal
                console.log('Iniciando petición AJAX a:', `/api/branches/${branchId}/routes`);
                
                fetch(`/api/branches/${branchId}/routes`)
                    .then(response => {
                        console.log('Respuesta AJAX recibida:', response);
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        const routeSelect = document.getElementById('route');
                        // Limpiar el select
                        routeSelect.innerHTML = '<option value="">Seleccione ruta</option>';
                        
                        // Verificar que data sea un array antes de usar forEach
                        if (Array.isArray(data)) {
                            // Agregar las nuevas opciones
                            data.forEach(route => {
                                console.log('Añadiendo ruta:', route);
                                const option = document.createElement('option');
                                option.value = route.id;
                                option.textContent = route.name;
                                routeSelect.appendChild(option);
                            });
                        } else if (data && typeof data === 'object') {
                            // Si data es un objeto, intentar iterar sobre sus propiedades
                            console.log('Datos recibidos como objeto, no como array');
                            for (const key in data) {
                                if (data.hasOwnProperty(key) && typeof data[key] === 'object') {
                                    const route = data[key];
                                    console.log('Añadiendo ruta (desde objeto):', route);
                                    const option = document.createElement('option');
                                    option.value = route.id || key;
                                    option.textContent = route.name || key;
                                    routeSelect.appendChild(option);
                                }
                            }
                        } else {
                            console.error('Formato de datos inesperado:', data);
                            alert('El servidor devolvió un formato de datos inesperado.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar las rutas:', error);
                        alert('Error al cargar las rutas. Por favor, inténtelo de nuevo más tarde.');
                        
                        // Agregar opción por defecto en caso de error
                        const routeSelect = document.getElementById('route');
                        routeSelect.innerHTML = '<option value="">Seleccione ruta (error al cargar)</option>';
                    });
            }
        });
    }
    
    // Botón cerrar
    if (document.getElementById('btnCerrar')) {
        document.getElementById('btnCerrar').addEventListener('click', function() {
            console.log('Botón cerrar clickeado');
            window.location.href = '{{ url("client") }}';
        });
    }
    
    // Cerrar con el botón de la cabecera
    const closeBtn = document.querySelector('.card-header .btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            console.log('Botón cerrar header clickeado');
            window.location.href = '{{ url("client") }}';
        });
    }
    
    function calculateAmounts() {
        console.log('Función calculateAmounts ejecutada');
        
        const amount = parseFloat(document.getElementById('requested_amount').value) || 0;
        const interestRate = parseFloat(document.getElementById('interest_rate').value) || 0;
        const term = parseInt(document.getElementById('term').value) || 1;
        
        console.log('Valores calculados:', {amount, interestRate, term});
        
        // Calcular beneficio (interés)
        const benefit = amount * (interestRate / 100);
        
        // Mostrar resultados
        document.getElementById('benefit').value = benefit.toFixed(2);
        
        // Calcular recuperación (monto total a pagar)
        const recovery = amount + benefit;
        document.getElementById('recovery').value = recovery.toFixed(2);
        
        console.log('Resultados calculados:', {benefit, recovery});
    }
});
</script>
@endsection
