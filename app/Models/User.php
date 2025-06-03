<?php

namespace App\Models;

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
        'name', 'email', 'password', 'level', 'last_name', 'address', 'province',
        'phone', 'nit', 'status', 'lng', 'lat', 'country', 'active_user',
        'business_name', 'business_sector', 'economic_activity', 'tax_id',
        'annual_revenue', 'employee_count', 'founding_date', 'legal_representative',
        'role', 'username'
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
     * Definición de roles disponibles en el sistema
     */
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_CAJA = 'caja';
    const ROLE_COLECTOR = 'colector';
    const ROLE_USER = 'user'; // Cliente normal
    
    /**
     * Verifica si se debe omitir la verificación de permisos
     * para el usuario actual
     */
    public function shouldBypassPermissionChecks()
    {
        return isset($this->bypassAuthChecks) && $this->bypassAuthChecks === true;
    }
    
    /**
     * Determina si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        
        // Verificar tanto por role como por level para compatibilidad
        return $this->role === $role || $this->level === $role;
    }
    
    /**
     * Determina si el usuario es un superadministrador
     */
    public function isSuperAdmin()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_SUPERADMIN || $this->level === 'admin';
    }
    
    /**
     * Determina si el usuario es un administrador
     */
    public function isAdmin()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_ADMIN || $this->role === self::ROLE_SUPERADMIN || $this->level === 'admin';
    }
    
    /**
     * Determina si el usuario es un supervisor
     */
    public function isSupervisor()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_SUPERVISOR;
    }
    
    /**
     * Determina si el usuario pertenece a caja
     */
    public function isCaja()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_CAJA;
    }
    
    /**
     * Determina si el usuario es un colector
     */
    public function isColector()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_COLECTOR;
    }
    
    /**
     * Determina si el usuario es un cliente
     */
    public function isClient()
    {
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        return $this->role === self::ROLE_USER;
    }
    
    /**
     * Relación con créditos (para clientes)
     */
    public function credits()
    {
        return $this->hasMany(Credit::class, 'id_user');
    }
    
    /**
     * Relación con solicitudes de crédito (para clientes)
     */
    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class, 'client_id');
    }
    
    /**
     * Relación con el rol del usuario
     */
    public function userRole()
    {
        return $this->belongsTo(Role::class, 'role', 'slug');
    }
    
    /**
     * Método seguro para obtener el nombre del rol
     * Evita errores cuando el rol no existe
     */
    public function getRoleName()
    {
        $role = $this->userRole;
        return $role ? $role->name : ucfirst($this->role);
    }
    
    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     */
    public function hasModuleAccess($module)
    {
        // Si debe omitir verificaciones, devolver true
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        
        // Si es superadmin o admin, siempre tiene acceso
        if ($this->isSuperAdmin() || $this->role === self::ROLE_ADMIN) {
            return true;
        }
        
        // Compatibilidad con nivel de usuario (versión anterior)
        if ($this->level === 'admin') {
            return true;
        }
        
        // Para menús y compatibilidad, si no está en modo estricto, permitir acceso
        if (!config('app.strict_permissions', false)) {
            return true;
        }
        
        // Obtenemos el rol del usuario
        $role = $this->userRole;
        
        if (!$role) {
            // Intentar obtener rol directamente de la base de datos
            try {
                $roleModel = \App\Models\Role::where('slug', $this->role)->first();
                if ($roleModel) {
                    return $roleModel->hasModuleAccess($module);
                }
            } catch (\Exception $e) {
                \Log::error("Error al obtener rol: " . $e->getMessage());
            }
            
            return false;
        }
        
        // Verificamos si el rol tiene acceso al módulo
        return $role->hasModuleAccess($module);
    }
    
    /**
     * Relación con el agente
     */
    public function agent()
    {
        return $this->hasOne(Agent::class, 'user_id');
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Si debe omitir verificaciones, devolver true
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        
        // Si es administrador, tiene todos los permisos
        if ($this->role === 'admin') {
            return true;
        }
        
        // Verificar si el usuario tiene el permiso asignado
        if (!$this->permissions) {
            return false;
        }
        
        $userPermissions = is_array($this->permissions) ? $this->permissions : json_decode($this->permissions, true);
        
        if (!$userPermissions) {
            return false;
        }
        
        return in_array($permission, $userPermissions);
    }
} 