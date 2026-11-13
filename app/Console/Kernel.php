<?php

namespace App\Console;

use App\Mail\DatabaseTablesCounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * This app uses the Laravel 10 structure (bootstrap/app.php binds this
     * kernel), so the scheduler reads this method, not routes/console.php.
     * In production, supervisord runs `php artisan schedule:work`
     * (docker/supervisord.conf, CONTAINER_ROLE=all or scheduler).
     *
     * Bills are never deleted on a schedule: a business must keep its sales
     * records for years (for example for VAT).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Permanently delete the businesses that were deleted more than
        // constants.deletedBusinessRetentionDays days ago (all their data).
        $schedule->command('business:purge-deleted')->daily()->withoutOverlapping();

        // Bills written by the old code during a rolling deploy get their sale
        // values (the entrypoint also runs it after the migrations).
        $schedule->command('bills:fill-sale-values')->hourly()->withoutOverlapping();

        //email me database table counts
        $schedule->call(function () {
            $businesses = DB::table('business')->count();
            $users = DB::table('users')->count();
            $products = DB::table('products')->count();
            $bills = DB::table('bills')->count();
            Log::warning('Daily Schedule: Email Me Database Tables Counts: businesses(' . $businesses . '), users(' . $users . '), products(' . $products . '), bills(' . $bills . ').');
            // ADMIN_REPORT_EMAIL; no address = no email.
            if ($to = config('mail.admin_report_address'))
                Mail::to($to)->send(new DatabaseTablesCounts($businesses,$users,$products,$bills));
        })->daily()->name('Email Me Database Tables Counts');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
