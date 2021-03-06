<?php

namespace App\Console;

use App\Models\Admin\ScheduledTasksExecuted;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {



        // Checking if MYSQL DUMP PATH is set, if so running the DB Backup Command
        if(setting()->get("db_mysql_dump_path") != null)
        {
            $schedule->command('backup:run --only-db')
                ->daily()
                ->onSuccess(function () {
                    $task = new ScheduledTasksExecuted();
                    $task->name = 'Daily Database Backup';
                    $task->command = 'backup:run --only-db';
                    $task->status = 'Success';
                    $task->ran_at = Now();
                    $task->save();
                })
                ->onFailure(function () {
                    $task = new ScheduledTasksExecuted();
                    $task->name = 'Daily Database Backup Failed';
                    $task->command = 'backup:run --only-db';
                    $task->status = 'Failed';
                    $task->ran_at = Now();
                    $task->save();
                });
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
