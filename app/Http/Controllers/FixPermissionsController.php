<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;

class FixPermissionsController extends Controller
{
    /**
     * Arregla los permisos del sistema, creando la tabla role_module_permissions
     * si no existe, y añadiendo los permisos necesarios para los roles.
     */
    public function fixPermissions()
    {
        try {
            $message = "";
            
            // 1. Verificar si la tabla existe y crearla si no
            if (!Schema::hasTable('role_module_permissions')) {
                $message .= "La tabla role_module_permissions no existe, creándola... ";
                
                Schema::create('role_module_permissions', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('role_id');
                    $table->string('module', 50);
                    $table->boolean('has_access')->default(false);
                    $table->json('permissions')->nullable();
                    $table->timestamps();
                    
                    $table->index('role_id');
                    $table->index('module');
                    
                    // No ponemos foreign key porque podría fallar si la tabla roles no existe
                    // o tiene un nombre diferente
                    
                    $table->unique(['role_id', 'module']);
                });
                
                $message .= "Tabla creada con éxito. ";
            } else {
                $message .= "La tabla role_module_permissions ya existe. ";
            }
            
            // 2. Verificar si tenemos la tabla de roles
            if (!Schema::hasTable('roles')) {
                $message .= "La tabla roles no existe, no se pueden añadir permisos. ";
                return redirect('/home')->with('warning', $message);
            }
            
            // 3. Insertar permisos para los roles existentes
            $roles = DB::table('roles')->get();
            $message .= "Encontrados " . $roles->count() . " roles. ";
            
            // Módulos del sistema
            $modules = [
                'dashboard',
                'clientes',
                'creditos',
                'pagos',
                'cobranzas',
                'reportes',
                'configuracion',
                'usuarios',
                'contabilidad',
                'auditoria'
            ];
            
            // Contadores para el mensaje
            $created = 0;
            $updated = 0;
            
            foreach ($roles as $role) {
                foreach ($modules as $module) {
                    // Por defecto, admin y superadmin tienen acceso a todo
                    $hasAccess = ($role->slug == 'superadmin' || $role->slug == 'admin');
                    
                    // Verificar si ya existe un registro
                    $existingPermission = DB::table('role_module_permissions')
                        ->where('role_id', $role->id)
                        ->where('module', $module)
                        ->first();
                    
                    if ($existingPermission) {
                        // Si es admin o superadmin, asegurarse de que tiene acceso
                        if ($hasAccess && !$existingPermission->has_access) {
                            DB::table('role_module_permissions')
                                ->where('id', $existingPermission->id)
                                ->update([
                                    'has_access' => true,
                                    'updated_at' => now()
                                ]);
                            $updated++;
                        }
                    } else {
                        // Crear nuevo permiso
                        DB::table('role_module_permissions')->insert([
                            'role_id' => $role->id,
                            'module' => $module,
                            'has_access' => $hasAccess,
                            'permissions' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        $created++;
                    }
                }
            }
            
            $message .= "Se han creado $created permisos nuevos y actualizado $updated permisos existentes.";
            
            // 4. Limpiar cachés
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return redirect('/collection/actions')->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error al arreglar permisos: ' . $e->getMessage());
            return redirect('/home')->with('error', 'Error al arreglar permisos: ' . $e->getMessage());
        }
    }
}
