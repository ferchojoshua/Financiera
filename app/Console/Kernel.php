<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        Commands\ManagePermissions::class,
        \App\Console\Commands\CreatePaymentsTable::class,
        Commands\UpdateModulePermissions::class,
        Commands\AssignSuperAdminPermissions::class,
        Commands\TestRolePermissions::class,
        Commands\CleanDuplicatePermissions::class,
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
