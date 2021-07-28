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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'every_five_minutes')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyFiveMinutes();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'every_ten_minutes')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyTenMinutes();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'every_fifteen_minutes')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyFifteenMinutes();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'every_thirty_minutes')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyThirtyMinutes();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'hourly')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->hourly();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'every_three_hours')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyThreeHours();

        $schedule->call(function () {
            $rules = \App\third\TikTokRule::where('status', 'active')
                ->where('frequency', 'daily')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->daily();
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
