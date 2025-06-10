<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class GrantPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:grant {role} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant access to a module for a specific role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $roleSlug = $this->argument('role');
        $module = $this->argument('module');

        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            $this->error("Role '{$roleSlug}' not found.");
            return 1;
        }

        try {
            DB::table('role_module_permissions')->updateOrInsert(
                ['role_id' => $role->id, 'module' => $module],
                ['has_access' => true, 'created_at' => now(), 'updated_at' => now()]
            );

            $this->info("Permission for module '{$module}' granted to role '{$roleSlug}'.");
            return 0;

        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }
    }
} 