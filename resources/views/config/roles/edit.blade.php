@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Editar Rol</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('config.roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Nombre del Rol</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $role->name) }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="slug" class="col-md-4 col-form-label text-md-right">Slug</label>
                            <div class="col-md-6">
                                <input id="slug" type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $role->slug) }}" required {{ $role->isSystemRole() ? 'readonly' : '' }}>
                                <small class="form-text text-muted">Identificador único para el rol (solo letras minúsculas, números y guiones)</small>
                                @error('slug')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="description" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <hr>
                        <h5 class="mb-3">Permisos Asignados</h5>
                        
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input module-checkbox" type="checkbox" id="module_{{ $module }}" data-module="{{ $module }}">
                                        <label class="form-check-label" for="module_{{ $module }}">
                                            <strong>{{ ucfirst($module) }}</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" 
                                                           value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                           data-module="{{ $module }}" 
                                                           {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                           {{ $role->isSystemRole() && in_array($permission->id, $rolePermissions) ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                        <small class="d-block text-muted">{{ $permission->description }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($role->isSystemRole())
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> Este es un rol del sistema. Algunos permisos fundamentales no se pueden modificar.
                            </div>
                        @endif
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Actualizar Rol
                                </button>
                                <a href="{{ route('config.roles.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancelar
                                </a>
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
        // Auto-generar slug desde el nombre (solo si no es un rol del sistema)
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        if (!slugInput.hasAttribute('readonly')) {
            nameInput.addEventListener('keyup', function() {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            });
        }
        
        // Seleccionar/deseleccionar todos los permisos de un módulo
        const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
        
        moduleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:not([disabled])`);
                
                permissionCheckboxes.forEach(permissionCheckbox => {
                    permissionCheckbox.checked = this.checked;
                });
            });
        });
        
        // Actualizar estado del checkbox del módulo cuando se seleccionan/deseleccionan permisos individuales
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox:not([disabled])');
        
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${module}"]`);
                const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:not([disabled])`);
                const checkedPermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked:not([disabled])`);
                
                moduleCheckbox.checked = modulePermissions.length === checkedPermissions.length;
                moduleCheckbox.indeterminate = checkedPermissions.length > 0 && checkedPermissions.length < modulePermissions.length;
            });
        });
        
        // Inicializar estado de los checkboxes de módulos
        moduleCheckboxes.forEach(moduleCheckbox => {
            const module = moduleCheckbox.dataset.module;
            const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:not([disabled])`);
            const checkedPermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked:not([disabled])`);
            
            moduleCheckbox.checked = modulePermissions.length === checkedPermissions.length && modulePermissions.length > 0;
            moduleCheckbox.indeterminate = checkedPermissions.length > 0 && checkedPermissions.length < modulePermissions.length;
        });
    });
</script>
@endsection 