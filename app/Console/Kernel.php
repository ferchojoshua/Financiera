<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ManagePermissions;
use App\Console\Commands\CleanDuplicatePermissions;
use App\Console\Commands\TestRolePermissions;
use App\Console\Commands\GrantPermission;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CreateBasicRoles::class,
        Commands\InsertRoleModulePermissions::class,
        Commands\CreateAdminWallets::class,
        ManagePermissions::class,
        \App\Console\Commands\CreatePaymentsTable::class,
        Commands\UpdateModulePermissions::class,
        Commands\AssignSuperAdminPermissions::class,
        TestRolePermissions::class,
        CleanDuplicatePermissions::class,
        GrantPermission::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
