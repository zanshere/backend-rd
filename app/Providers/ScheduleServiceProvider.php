<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            // Update offline users
            $schedule->call(function () {
                \App\Models\User::where('is_online', true)
                    ->where('last_activity_at', '<', now()->subMinutes(5))
                    ->chunk(100, function ($users) {
                        foreach ($users as $user) {
                            $user->update(['is_online' => false]);

                            // Broadcast event
                            broadcast(new \App\Events\UserOnlineStatus($user->id, false))->toOthers();
                        }
                    });
            })->everyMinute();

            // Cleanup old notifications (30 days)
            $schedule->call(function () {
                \Illuminate\Support\Facades\DB::table('notifications')
                    ->where('created_at', '<', now()->subDays(30))
                    ->delete();
            })->dailyAt('03:00');

            // Cleanup old chat data (90 days)
            $schedule->call(function () {
                \App\Models\MessageAttachment::where('created_at', '<', now()->subDays(90))
                    ->chunk(100, function ($attachments) {
                        foreach ($attachments as $attachment) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->path);
                            $attachment->delete();
                        }
                    });
            })->dailyAt('04:00');
        });
    }
}
