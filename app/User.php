<?php

namespace App;

use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Clase wrapper para compatibilidad con código antiguo
 * Esta clase hereda todas las propiedades y métodos de Models\User
 * y agrega los métodos específicos necesarios para la compatibilidad
 */
class User extends ModelsUser
{
    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     * Versión específica para compatibilidad con código antiguo
     */
    public function hasModuleAccess($module)
    {
        // Si hay bypass de permisos, permitir acceso
        if ($this->shouldBypassPermissionChecks()) {
            return true;
        }
        
        // Si es superadmin o admin, siempre tiene acceso a todos los módulos
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }
        
        // Para el módulo de clientes, todos tienen acceso excepto usuarios regulares
        if ($module === 'clientes' && $this->level !== 'user' && $this->role !== 'user') {
            return true;
        }
        
        // Para el módulo de créditos, todos tienen acceso excepto usuarios regulares
        if ($module === 'creditos' && $this->level !== 'user' && $this->role !== 'user') {
            return true;
        }
        
        try {
            // Intentar obtener el rol del usuario desde la tabla roles
            $roleRecord = null;
            
            // Primero buscar por role
            if (!empty($this->role)) {
                $roleRecord = DB::table('roles')->where('slug', $this->role)->first();
            }
            
            // Si no se encontró, buscar por level
            if (!$roleRecord && !empty($this->level)) {
                $roleRecord = DB::table('roles')->where('slug', $this->level)->first();
            }
            
            // Si encontramos un rol válido, verificar permisos
            if ($roleRecord) {
                // Verificar permiso específico para el módulo
                $hasAccess = DB::table('role_module_permissions')
                    ->where('role_id', $roleRecord->id)
                    ->where('module', $module)
                    ->where('has_access', true)
                    ->exists();
                    
                if ($hasAccess) {
                    return true;
                }
                
                // Si no tiene permiso específico, verificar si tiene permiso de administración
                $adminAccess = DB::table('role_module_permissions')
                    ->where('role_id', $roleRecord->id)
                    ->where('module', 'admin')
                    ->where('has_access', true)
                    ->exists();
                    
                if ($adminAccess) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            // Si hay un error, registrarlo pero no bloquear al usuario
            Log::error("Error verificando permisos para el usuario ID {$this->id}: " . $e->getMessage());
        }
        
        // Por defecto, denegar acceso
        return false;
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
        return isset($this->attributes['level']) ? $this->attributes['level'] : null;
    }
    
    /**
     * Relación con sucursal
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
