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
        // Si es superadmin o admin, siempre tiene acceso a todos los módulos
        if ($this->slug === 'superadmin' || $this->slug === 'admin') {
            return true;
        }
        
        // Compatibilidad con versiones anteriores del sistema
        if (in_array($module, ['routes', 'cash', 'reports', 'collection', 'clients', 'config', 'pymes', 'security'])) {
            // Verificar permiso en tabla de permisos por módulo
            $hasAccess = \DB::table('role_module_permissions')
                ->where('role_id', $this->id)
                ->where('module', $module)
                ->where('has_access', true)
                ->exists();
                
            if ($hasAccess) {
                return true;
            }
            
            // Si no encuentra permiso específico, verificar permisos globales
            $adminAccess = \DB::table('role_module_permissions')
                ->where('role_id', $this->id)
                ->where('module', 'admin')
                ->where('has_access', true)
                ->exists();
                
            return $adminAccess;
        }
        
        // Si no es un módulo estándar, permitir acceso por defecto
        return true;
    }
} 