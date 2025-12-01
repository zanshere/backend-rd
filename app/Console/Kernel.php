<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Update offline users every minute
        $schedule->job(new \App\Jobs\UpdateOfflineUsers())->everyMinute();

        // Clean up old notifications (30 days old)
        $schedule->call(function () {
            \App\Models\Notification::where('created_at', '<', now()->subDays(30))->delete();
        })->daily();

        // Clean up old message attachments (90 days old)
        $schedule->call(function () {
            $oldAttachments = \App\Models\MessageAttachment::where('created_at', '<', now()->subDays(90))->get();

            foreach ($oldAttachments as $attachment) {
                // Delete file from storage
                \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->path);
                // Delete record from database
                $attachment->delete();
            }
        })->daily();

        // Update user online status periodically
        $schedule->call(function () {
            \App\Models\User::where('is_online', true)
                ->where('last_activity_at', '<', now()->subMinutes(5))
                ->update(['is_online' => false]);
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
