<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'level', 'role', 'business_name', 'tax_id', 
        'phone', 'address', 'city', 'sector', 'province', 'nit', 'last_name',
        'lat', 'lng', 'status', 'branch_id', 'gender', 'house_type', 'civil_status',
        'spouse_name', 'spouse_job', 'spouse_phone', 'business_type', 'business_time',
        'sales_good', 'sales_bad', 'weekly_average', 'net_profit'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * Verifica si se debe omitir la verificación de permisos
     * para el usuario actual
     */
    public function shouldBypassPermissionChecks()
    {
        return isset($this->bypassAuthChecks) && $this->bypassAuthChecks === true;
    }
    
    /**
     * Comprueba si el usuario es administrador
     */
    public function isAdmin()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->level === 'admin' || $this->role === 'admin';
    }
    
    /**
     * Comprueba si el usuario es superadmin
     */
    public function isSuperAdmin()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->level === 'superadmin' || $this->role === 'superadmin';
    }
    
    /**
     * Determina si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === $role;
    }
    
    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     */
    public function hasModuleAccess($module)
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        
        // Delegar a la implementación del rol si existe
        if (method_exists($this, 'userRole')) {
            $role = $this->userRole;
            if ($role && method_exists($role, 'hasModuleAccess')) {
                return $role->hasModuleAccess($module);
            }
        }
        
        // Por defecto, admin y superadmin tienen acceso a todo
        return $this->isAdmin() || $this->isSuperAdmin();
    }
    
    /**
     * Obtiene el rol real del usuario (compatibilidad entre level y role)
     */
    public function getRoleAttribute()
    {
        // Si ya está definido role, usar ese valor
        if (!empty($this->attributes['role'])) {
            return $this->attributes['role'];
        }
        
        // Si no, usar level
        return $this->attributes['level'] ?? null;
    }
    
    /**
     * Relación con sucursal
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
