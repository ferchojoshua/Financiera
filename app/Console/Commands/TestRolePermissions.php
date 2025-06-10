<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestRolePermissions extends Command
{
    protected $signature = 'test:role-permissions {role? : Slug del rol a probar}';
    protected $description = 'Prueba los permisos de roles en el sistema';

    public function handle()
    {
        $roleSlug = $this->argument('role');
        
        if ($roleSlug) {
            $this->testSpecificRole($roleSlug);
        } else {
            $this->testAllRoles();
        }
    }

    protected function testAllRoles()
    {
        $roles = Role::all();
        
        foreach ($roles as $role) {
            $this->testSpecificRole($role->slug);
        }
    }

    protected function testSpecificRole($slug)
    {
        $role = Role::where('slug', $slug)->first();
        
        if (!$role) {
            $this->error("Rol no encontrado: {$slug}");
            return;
        }

        $this->info("\nProbando rol: {$role->name} ({$role->slug})");
        $this->info("----------------------------------------");

        // Obtener todos los módulos del sistema
        $modules = DB::table('role_module_permissions')
            ->select('module')
            ->distinct()
            ->pluck('module');

        // Probar cada módulo
        foreach ($modules as $module) {
            $hasAccess = DB::table('role_module_permissions')
                ->where('role_id', $role->id)
                ->where('module', $module)
                ->where('has_access', true)
                ->exists();

            $status = $hasAccess ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("{$status} {$module}");
        }

        // Crear un usuario temporal con este rol para pruebas
        $testUser = new User();
        $testUser->role = $role->slug;
        
        // Probar algunos casos especiales
        $this->info("\nPruebas especiales:");
        $this->line("hasModuleAccess('dashboard'): " . ($testUser->hasModuleAccess('dashboard') ? 'true' : 'false'));
        $this->line("hasModuleAccess('admin'): " . ($testUser->hasModuleAccess('admin') ? 'true' : 'false'));
        $this->line("shouldBypassPermissionChecks(): " . ($testUser->shouldBypassPermissionChecks() ? 'true' : 'false'));
    }
} 