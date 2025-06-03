@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Permisos de Acceso</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Configure los permisos de acceso a los diferentes módulos del sistema para cada rol.
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/config/permisos/update') }}" id="permisos-form">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        
                        @foreach($roles as $role)
                            @if($role->slug === 'superadmin')
                                <input type="hidden" name="permissions[{{ $role->id }}][dashboard]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][wallet]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][clients_regular]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][clients_pyme]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][credit_requests]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][scoring]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][guarantees]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][financial_products]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][simulator]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][payments]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][collections]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][payment_agreements]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][reports]" value="1">
                                <input type="hidden" name="permissions[{{ $role->id }}][canceled_reports]" value="1">
                            @endif
                        @endforeach
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Módulo</th>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'superadmin')
                                                <th>{{ $role->name }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) }}"><strong>Módulos Principales</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Dashboard</td>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'superadmin')
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][dashboard]" value="1" 
                                                        {{ isset($permissions[$role->id]['dashboard']) && $permissions[$role->id]['dashboard'] ? 'checked' : '' }}
                                                        {{ $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Billetera</td>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'superadmin')
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][wallet]" value="1" 
                                                        {{ isset($permissions[$role->id]['wallet']) && $permissions[$role->id]['wallet'] ? 'checked' : '' }}
                                                        {{ $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) }}"><strong>Gestión de Clientes</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Clientes Regulares</td>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'superadmin')
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][clients_regular]" value="1" 
                                                        {{ isset($permissions[$role->id]['clients_regular']) && $permissions[$role->id]['clients_regular'] ? 'checked' : '' }}
                                                        {{ $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Clientes PYME</td>
                                        @foreach($roles as $role)
                                            @if($role->slug !== 'superadmin')
                                                <td class="text-center">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][clients_pyme]" value="1" 
                                                        {{ isset($permissions[$role->id]['clients_pyme']) && $permissions[$role->id]['clients_pyme'] ? 'checked' : '' }}
                                                        {{ $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Créditos y Préstamos</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Solicitudes de Crédito</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][credit_requests]" value="1" 
                                                    {{ isset($permissions[$role->id]['credit_requests']) && $permissions[$role->id]['credit_requests'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Análisis y Scoring</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][scoring]" value="1" 
                                                    {{ isset($permissions[$role->id]['scoring']) && $permissions[$role->id]['scoring'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Garantías</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][guarantees]" value="1" 
                                                    {{ isset($permissions[$role->id]['guarantees']) && $permissions[$role->id]['guarantees'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Productos Financieros</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][financial_products]" value="1" 
                                                    {{ isset($permissions[$role->id]['financial_products']) && $permissions[$role->id]['financial_products'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Simulador</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][simulator]" value="1" 
                                                    {{ isset($permissions[$role->id]['simulator']) && $permissions[$role->id]['simulator'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Pagos y Cobranza</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Pagos</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][payments]" value="1" 
                                                    {{ isset($permissions[$role->id]['payments']) && $permissions[$role->id]['payments'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' || $role->slug === 'caja' || $role->slug === 'supervisor' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Cobranza</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][collections]" value="1" 
                                                    {{ isset($permissions[$role->id]['collections']) && $permissions[$role->id]['collections'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' || $role->slug === 'colector' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Acuerdos de Pago</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][payment_agreements]" value="1" 
                                                    {{ isset($permissions[$role->id]['payment_agreements']) && $permissions[$role->id]['payment_agreements'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Reportes y Contabilidad</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Reportes</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][reports]" value="1" 
                                                    {{ isset($permissions[$role->id]['reports']) && $permissions[$role->id]['reports'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' || $role->slug === 'supervisor' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Cancelados</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][canceled_reports]" value="1" 
                                                    {{ isset($permissions[$role->id]['canceled_reports']) && $permissions[$role->id]['canceled_reports'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>

                                    <tr class="table-primary">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Administración del Sistema</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Gestión de Usuarios</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][manage_users]" value="1" 
                                                    {{ isset($permissions[$role->id]['manage_users']) && $permissions[$role->id]['manage_users'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}
                                                    {{ !($role->slug === 'superadmin' || $role->slug === 'admin') ? 'disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Configuración del Sistema</td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][system_config]" value="1" 
                                                    {{ isset($permissions[$role->id]['system_config']) && $permissions[$role->id]['system_config'] ? 'checked' : '' }}
                                                    {{ $role->slug === 'superadmin' || $role->slug === 'admin' ? 'checked disabled' : '' }}
                                                    {{ !($role->slug === 'superadmin' || $role->slug === 'admin') ? 'disabled' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Guardar Permisos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

<script>
// Asegurar que el formulario se envíe como POST
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('permisos-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Evitar comportamiento predeterminado
            e.preventDefault();
            
            // Crear un nuevo formulario oculto con método POST
            var hiddenForm = document.createElement('form');
            hiddenForm.method = 'POST';
            hiddenForm.action = '{{ url("/config/permisos/update") }}';
            hiddenForm.style.display = 'none';
            
            // Añadir el token CSRF
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            hiddenForm.appendChild(csrfToken);
            
            // Copiar todos los campos del formulario original
            var formData = new FormData(form);
            for (var pair of formData.entries()) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = pair[0];
                input.value = pair[1];
                hiddenForm.appendChild(input);
            }
            
            // Añadir el formulario al documento y enviarlo
            document.body.appendChild(hiddenForm);
            hiddenForm.submit();
        });
    }
});
</script> 