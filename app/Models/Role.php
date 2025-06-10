<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'created_by',
        'updated_by',
        'is_active'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos
     */
    protected $casts = [
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relación muchos a muchos con permisos
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Usuario que creó el rol
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Usuario que actualizó el rol
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Usuarios que tienen este rol
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    /**
     * Comprueba si es un rol del sistema
     */
    public function isSystemRole()
    {
        return $this->is_system;
    }

    /**
     * Comprueba si el rol tiene un permiso específico
     */
    public function hasPermission($permissionSlug)
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Comprueba si el rol tiene acceso a un módulo específico
     */
    public function hasModuleAccess($module)
    {
        // Si es superadmin, siempre tiene acceso a todos los módulos
        if ($this->slug === 'superadmin') {
            return true;
        }
        
        // Verificar permiso en tabla de permisos por módulo
        return \DB::table('role_module_permissions')
            ->where('role_id', $this->id)
            ->where('module', $module)
            ->where('has_access', true)
            ->exists();
    }

    /**
     * Asigna permisos de módulo de manera segura evitando duplicados
     */
    public function assignModulePermission($module, $hasAccess = false)
    {
        return \DB::table('role_module_permissions')
            ->updateOrInsert(
                [
                    'role_id' => $this->id,
                    'module' => $module
                ],
                [
                    'has_access' => $hasAccess,
                    'updated_at' => now()
                ]
            );
    }

    /**
     * Asigna múltiples permisos de módulo de manera segura
     */
    public function assignModulePermissions(array $permissions)
    {
        foreach ($permissions as $module => $hasAccess) {
            $this->assignModulePermission($module, $hasAccess);
        }
    }
} 