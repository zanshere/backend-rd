<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOfflineUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $threshold = now()->subMinutes(5);

        $usersToMarkOffline = User::where('is_online', true)
            ->where('last_activity_at', '<', $threshold)
            ->get();

        foreach ($usersToMarkOffline as $user) {
            $user->update([
                'is_online' => false,
            ]);

            // Broadcast offline status
            broadcast(new \App\Events\UserOnlineStatus($user->id, false))->toOthers();
        }
    }
}
