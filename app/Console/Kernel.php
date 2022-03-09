<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;

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
        // Product medias temporary images (delete the whole folder every day
        // at night)
        $schedule->call(function () {
            Storage::deleteDirectory('public/tmp');
        })->dailyAt('01:00');

        // Refresh shipping statuses
        $schedule->call(function () {
            \App\third\DropinShipping::refreshTable();
        })->dailyAt('00:00');

        // Refresh and leads whatsapp message
        $schedule->call(function () {
            $leads = \App\third\DropinLead::where('archived', 0);
            foreach ($leads as $lead) {
                $lead->refreshWhatsappInfo();
            }
        })->dailyAt('00:00');

        // Facebook Monitors reports
        $schedule->call(function () {
            $monitors = \App\third\FacebookMonitor::where('archived', 0)->get();
            foreach ($monitors as $monitor) {
                $monitor->createReport();
            }
        })->dailyAt('00:05');

        // Facebook Monitors strategies
        $schedule->call(function () {
            $monitors = \App\third\FacebookMonitor::where('archived', 0)->get();
            foreach ($monitors as $monitor) {
                if ($monitor->strategy === null) {
                    continue;
                }
                foreach ($monitor->strategy as $key => $value) {
                    if (isset($value['time_check']) && $value['time_check'] != 'every_thirty_minutes') {
                        continue;
                    }
                    $params = $value;
                    $params['action_type'] = $key;
                    if (isset($value['enabled']) && $value['enabled']) {
                        $monitor->runStrategy($params);
                    }
                }
            }
        })->everyThirtyMinutes();
        $schedule->call(function () {
            $monitors = \App\third\FacebookMonitor::where('archived', 0)->get();
            foreach ($monitors as $monitor) {
                if ($monitor->strategy === null) {
                    continue;
                }
                foreach ($monitor->strategy as $key => $value) {
                    if (isset($value['time_check']) && $value['time_check'] != 'hourly') {
                        continue;
                    }
                    $params = $value;
                    $params['action_type'] = $key;
                    if (isset($value['enabled']) && $value['enabled']) {
                        $monitor->runStrategy($params);
                    }
                }
            }
        })->hourly();
        $schedule->call(function () {
            $monitors = \App\third\FacebookMonitor::where('archived', 0)->get();
            foreach ($monitors as $monitor) {
                if ($monitor->strategy === null) {
                    continue;
                }
                foreach ($monitor->strategy as $key => $value) {
                    if (isset($value['time_check']) && $value['time_check'] != 'every_two_hours') {
                        continue;
                    }
                    $params = $value;
                    $params['action_type'] = $key;
                    if (isset($value['enabled']) && $value['enabled']) {
                        $monitor->runStrategy($params);
                    }
                }
            }
        })->everyTwoHours();

        // Facebook Monitors complete strategy
        $schedule->call(function () {
            $monitors = \App\third\FacebookMonitor::where('archived', 0)->get();
            foreach ($monitors as $monitor) {
                $monitor->completeStrategy();
            }
        })->dailyAt('00:05');


        // Tiktok Rules
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
                ->where('frequency', 'every_two_hours')->get();
            foreach($rules as $rule) {
                $rule->apply();
            }
        })->everyTwoHours();

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

        // Warehouse leads update
        $schedule->call(function () {
            $dateStart = date('Y-m-d', strtotime('-7 day'));
            $dateEnd   = date('Y-m-d');
            $leads = \App\third\WarehouseLead::where('archived', 0)
                ->where('created_at', '>=', $dateStart . ' 00:00:00')
                ->where('created_at', '<=', $dateEnd   . ' 23:59:59')
                ->where(function($query) {
                    $query->where('status', 'to recall')
                        ->orWhere('status', 'no answer');
                })
                ->get();
            foreach ($leads as $lead) {
                // Reset contact check
                $info = $lead->info;
                if ($info === null) {
                    $info = [];
                }
                if (isset($info['contact'])) {
                    $info['contact'] = false;
                }

                // Update delivery info
                $delivery = null;
                if (isset($info['delivery']) && isset($info['delivery']['order_number'])) {
                    $elogy = new \Driverfury\Elogy\Elogy(
                        env('ELOGY_API_TOKEN'),
                        env('ELOGY_EMAIL'),
                        env('ELOGY_PASSWORD'));
                    $delivery = $elogy->getOrderByNumber($info['delivery']['order_number']);
                    $info['delivery'] = $delivery;
                }

                $lead->info = $info;
                $lead->save();
            }
        })->everyFiveMinutes();
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
