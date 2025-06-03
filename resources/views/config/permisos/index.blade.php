@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Permisos de Acceso</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Configure los permisos de acceso a los diferentes módulos del sistema para cada rol.
                    </div>
                    
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('config.permisos.update') }}">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Módulo</th>
                                        @foreach($roles as $role_id => $role_name)
                                            <th class="text-center">{{ $role_name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Módulos principales -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Módulos Principales</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Dashboard</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_dashboard" name="permisos[{{ $role_id }}][]" value="dashboard" {{ in_array('dashboard', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_dashboard"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Billetera</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_billetera" name="permisos[{{ $role_id }}][]" value="billetera" {{ in_array('billetera', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_billetera"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Gestión de Clientes -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Gestión de Clientes</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Clientes Regulares</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_clientes" name="permisos[{{ $role_id }}][]" value="clientes" {{ in_array('clientes', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_clientes"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Clientes PYME</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_pymes" name="permisos[{{ $role_id }}][]" value="pymes" {{ in_array('pymes', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_pymes"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Créditos y Préstamos -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Créditos y Préstamos</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Solicitudes de Crédito</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_solicitudes" name="permisos[{{ $role_id }}][]" value="solicitudes" {{ in_array('solicitudes', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_solicitudes"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Análisis y Scoring</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_analisis" name="permisos[{{ $role_id }}][]" value="analisis" {{ in_array('analisis', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_analisis"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Garantías</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_garantias" name="permisos[{{ $role_id }}][]" value="garantias" {{ in_array('garantias', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_garantias"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Productos Financieros</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_productos" name="permisos[{{ $role_id }}][]" value="productos" {{ in_array('productos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_productos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Simulador</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_simulador" name="permisos[{{ $role_id }}][]" value="simulador" {{ in_array('simulador', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_simulador"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Pagos y Cobranza -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Pagos y Cobranza</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Pagos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_pagos" name="permisos[{{ $role_id }}][]" value="pagos" {{ in_array('pagos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_pagos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Cobranza</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_cobranza" name="permisos[{{ $role_id }}][]" value="cobranza" {{ in_array('cobranza', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_cobranza"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Acuerdos de Pago</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_acuerdos" name="permisos[{{ $role_id }}][]" value="acuerdos" {{ in_array('acuerdos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_acuerdos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Reportes y Contabilidad -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Reportes y Contabilidad</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Reportes</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes" name="permisos[{{ $role_id }}][]" value="reportes" {{ in_array('reportes', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Cancelados</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes_cancelados" name="permisos[{{ $role_id }}][]" value="reportes_cancelados" {{ in_array('reportes_cancelados', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes_cancelados"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Desembolsos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes_desembolsos" name="permisos[{{ $role_id }}][]" value="reportes_desembolsos" {{ in_array('reportes_desembolsos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes_desembolsos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Activos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes_activos" name="permisos[{{ $role_id }}][]" value="reportes_activos" {{ in_array('reportes_activos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes_activos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Vencidos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes_vencidos" name="permisos[{{ $role_id }}][]" value="reportes_vencidos" {{ in_array('reportes_vencidos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes_vencidos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Reportes Por Cancelar</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_reportes_por_cancelar" name="permisos[{{ $role_id }}][]" value="reportes_por_cancelar" {{ in_array('reportes_por_cancelar', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_reportes_por_cancelar"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Cierre de Mes</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_cierre_mes" name="permisos[{{ $role_id }}][]" value="cierre_mes" {{ in_array('cierre_mes', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_cierre_mes"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Recuperación y Desembolsos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_recuperacion_desembolsos" name="permisos[{{ $role_id }}][]" value="recuperacion_desembolsos" {{ in_array('recuperacion_desembolsos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_recuperacion_desembolsos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Rutas y Cobranza -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Rutas y Cobranza</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Gestión de Rutas</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_rutas" name="permisos[{{ $role_id }}][]" value="rutas" {{ in_array('rutas', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_rutas"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Asignación de Créditos</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_asignacion_creditos" name="permisos[{{ $role_id }}][]" value="asignacion_creditos" {{ in_array('asignacion_creditos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_asignacion_creditos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Sistema y Auditoría -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Sistema y Auditoría</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Registro de Auditoría</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_auditoria" name="permisos[{{ $role_id }}][]" value="auditoria" {{ in_array('auditoria', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_auditoria"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    
                                    <!-- Configuración del Sistema -->
                                    <tr class="bg-light">
                                        <td colspan="{{ count($roles) + 1 }}"><strong>Configuración del Sistema</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Configuración General</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_configuracion" name="permisos[{{ $role_id }}][]" value="configuracion" {{ in_array('configuracion', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_configuracion"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Gestión de Usuarios</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_usuarios" name="permisos[{{ $role_id }}][]" value="usuarios" {{ in_array('usuarios', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_usuarios"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Permisos de Acceso</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_permisos" name="permisos[{{ $role_id }}][]" value="permisos" {{ in_array('permisos', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_permisos"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Preferencias del Sistema</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_preferencias" name="permisos[{{ $role_id }}][]" value="preferencias" {{ in_array('preferencias', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_preferencias"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>Información de la Empresa</td>
                                        @foreach($roles as $role_id => $role_name)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $role_id }}_empresa" name="permisos[{{ $role_id }}][]" value="empresa" {{ in_array('empresa', $permisos[$role_id] ?? []) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $role_id }}_empresa"></label>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="form-group row mt-4">
                            <div class="col-md-6 offset-md-3">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para seleccionar/deseleccionar todos los permisos de un rol
        const selectAllForRole = function(roleId) {
            const checkboxes = document.querySelectorAll(`input[name="permisos[${roleId}][]"]`);
            const selectAllChecked = document.getElementById(`select_all_${roleId}`).checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllChecked;
            });
        };
        
        // Agregar listeners para los checkboxes "Seleccionar todos"
        @foreach($roles as $role_id => $role_name)
            if (document.getElementById(`select_all_{{ $role_id }}`)) {
                document.getElementById(`select_all_{{ $role_id }}`).addEventListener('change', function() {
                    selectAllForRole('{{ $role_id }}');
                });
            }
        @endforeach
    });
</script>
@endsection 