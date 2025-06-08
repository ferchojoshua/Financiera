<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

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
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
    
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
     * Relación con créditos como agente
     */
    public function managedCredits()
    {
        return $this->hasMany(Credit::class, 'agent_id');
    }
    
    /**
     * Relación con pagos recolectados
     */
    public function collectedPayments()
    {
        return $this->hasMany(Payment::class, 'collected_by');
    }
    
    /**
     * Relación con rutas asignadas
     */
    public function assignedRoutes()
    {
        return $this->hasMany(Route::class, 'assigned_agent_id');
    }
    
    /**
     * Relación con clientes asignados
     */
    public function assignedClients()
    {
        return $this->hasMany(Client::class, 'assigned_agent_id');
    }
    
    /**
     * Relación con créditos aprobados
     */
    public function approvedCredits()
    {
        return $this->hasMany(Credit::class, 'approved_by');
    }
    
    /**
     * Obtener estadísticas de rendimiento del agente
     */
    public function getPerformanceStats($startDate = null, $endDate = null)
    {
        if (!$startDate) {
            $startDate = now()->startOfMonth();
        }
        
        if (!$endDate) {
            $endDate = now();
        }
        
        // Monto total recolectado en el período
        $collectedAmount = $this->collectedPayments()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
            
        // Número de pagos recolectados
        $collectedCount = $this->collectedPayments()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->count();
            
        // Créditos nuevos otorgados
        $newCredits = $this->managedCredits()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        // Monto total de créditos otorgados
        $totalCreditAmount = $this->managedCredits()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount_approved');
            
        return [
            'collected_amount' => $collectedAmount,
            'collected_count' => $collectedCount,
            'new_credits' => $newCredits,
            'total_credit_amount' => $totalCreditAmount,
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
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
        
        // Si es superadmin, siempre tiene acceso
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Si es admin, siempre tiene acceso (compatibilidad)
        if ($this->role === self::ROLE_ADMIN || $this->level === 'admin') {
            return true;
        }
        
        // Verificamos el permiso en la tabla role_module_permissions
        try {
            // Obtenemos el ID del rol
            $role = \App\Models\Role::where('slug', $this->role)->first();
            
            if ($role) {
                // Verificar acceso en la tabla de permisos
                $hasAccess = \DB::table('role_module_permissions')
                    ->where('role_id', $role->id)
                    ->where('module', $module)
                    ->where('has_access', true)
                    ->exists();
                
                return $hasAccess;
            }
            
            // Compatibilidad con versiones anteriores
            if (in_array($this->role, ['admin', 'superadmin']) || $this->level === 'admin') {
            return true;
        }
        
            // Modo debug - permitir acceso
            if (config('app.debug', false)) {
                \Log::info("DEBUG MODE: Permitiendo acceso a {$module} para usuario {$this->id} con rol {$this->role}");
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error("Error al verificar permisos: " . $e->getMessage());
            
            // En caso de error, permitir acceso en modo debug
            return config('app.debug', false);
        }
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