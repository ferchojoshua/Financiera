<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Clase Helper para gestión de permisos
 * Esta clase proporciona métodos para verificar permisos de usuario
 */
class PermissionHelper
{
    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     *
     * @param string|null $module Nombre del módulo a verificar
     * @param int|null $userId ID del usuario (opcional, por defecto el usuario actual)
     * @return bool True si tiene acceso, false en caso contrario
     */
    public static function hasModuleAccess($module, $userId = null)
    {
        try {
            // Si no se especifica un usuario, usar el actual
            if (is_null($userId) && Auth::check()) {
                $userId = Auth::id();
            }
            
            // Si no hay usuario, no tiene acceso
            if (is_null($userId)) {
                return false;
            }
            
            // Obtener el usuario
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return false;
            }
            
            // Administradores y superadmins tienen acceso completo
            if ($user->role === 'admin' || $user->role === 'superadmin') {
                return true;
            }
            
            // Para compatibilidad con código antiguo
            if ($user->deprecated_level === 'admin') {
                return true;
            }
            
            // Buscar el rol del usuario
            $role = DB::table('roles')->where('slug', $user->role)->first();
            if (!$role) {
                return false;
            }
            
            // Verificar permiso específico para el módulo
            $permission = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module', $module)
                ->where('has_access', true)
                ->first();
                
            if ($permission) {
                return true;
            }
            
            // Verificar permisos admin generales
            $adminPermission = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module', 'admin')
                ->where('has_access', true)
                ->first();
                
            if ($adminPermission) {
                return true;
            }
            
            // Por defecto, no tiene acceso
            return false;
            
        } catch (\Exception $e) {
            // Registrar el error pero no bloquear al usuario
            Log::error("Error verificando permisos: " . $e->getMessage());
            
            // En entorno de desarrollo, permitir acceso
            if (config('app.env') === 'local' || config('app.debug')) {
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Verifica si el usuario tiene un rol específico
     *
     * @param string|array $roles Rol o roles a verificar
     * @param int|null $userId ID del usuario (opcional, por defecto el usuario actual)
     * @return bool True si tiene el rol, false en caso contrario
     */
    public static function hasRole($roles, $userId = null)
    {
        try {
            // Si no se especifica un usuario, usar el actual
            if (is_null($userId) && Auth::check()) {
                $userId = Auth::id();
            }
            
            // Si no hay usuario, no tiene el rol
            if (is_null($userId)) {
                return false;
            }
            
            // Obtener el usuario
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return false;
            }
            
            // Convertir a array si es un string
            if (!is_array($roles)) {
                $roles = [$roles];
            }
            
            // Verificar si tiene alguno de los roles
            foreach ($roles as $role) {
                if ($user->role === $role) {
                    return true;
                }
                
                // Para compatibilidad con código antiguo
                if (isset($user->deprecated_level) && $user->deprecated_level === $role) {
                    return true;
                }
            }
            
            return false;
            
        } catch (\Exception $e) {
            // Registrar el error pero no bloquear al usuario
            Log::error("Error verificando roles: " . $e->getMessage());
            
            // En entorno de desarrollo, permitir acceso
            if (config('app.env') === 'local' || config('app.debug')) {
                return true;
            }
            
            return false;
        }
    }
    
    /**
     * Obtiene todos los módulos a los que tiene acceso un usuario
     *
     * @param int|null $userId ID del usuario (opcional, por defecto el usuario actual)
     * @return array Array con los nombres de los módulos
     */
    public static function getUserAccessibleModules($userId = null)
    {
        try {
            // Si no se especifica un usuario, usar el actual
            if (is_null($userId) && Auth::check()) {
                $userId = Auth::id();
            }
            
            // Si no hay usuario, devolver array vacío
            if (is_null($userId)) {
                return [];
            }
            
            // Obtener el usuario
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                return [];
            }
            
            // Administradores y superadmins tienen acceso completo
            if ($user->role === 'admin' || $user->role === 'superadmin' || $user->deprecated_level === 'admin') {
                // Obtener todos los módulos
                $modules = DB::table('role_module_permissions')
                    ->select('module')
                    ->distinct()
                    ->pluck('module')
                    ->toArray();
                    
                return $modules;
            }
            
            // Buscar el rol del usuario
            $role = DB::table('roles')->where('slug', $user->role)->first();
            if (!$role) {
                return [];
            }
            
            // Obtener módulos con acceso
            $modules = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('has_access', true)
                ->pluck('module')
                ->toArray();
                
            return $modules;
            
        } catch (\Exception $e) {
            // Registrar el error pero devolver array vacío
            Log::error("Error obteniendo módulos accesibles: " . $e->getMessage());
            return [];
        }
    }
} 