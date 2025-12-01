<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\UserOnlineStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Pastikan user terautentikasi
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $now = now();

        try {
            // Cek apakah kolom last_activity_at ada di database
            if (Schema::hasColumn('users', 'last_activity_at')) {
                $user->last_activity_at = $now;
            }

            // Cek apakah kolom is_online ada
            $shouldBeOnline = true; // Default: user dianggap online saat ini

            if (Schema::hasColumn('users', 'last_activity_at') && $user->last_activity_at) {
                // User dianggap online jika aktivitas dalam 5 menit terakhir
                $shouldBeOnline = $user->last_activity_at->greaterThan($now->subMinutes(5));
            }

            if (Schema::hasColumn('users', 'is_online')) {
                $wasOnline = $user->is_online ?? false;

                // Update status hanya jika berubah
                if ($user->is_online !== $shouldBeOnline) {
                    $user->is_online = $shouldBeOnline;
                }
            }

            // Simpan perubahan
            $user->save();

            // Broadcast event jika status berubah dari offline ke online
            // TAPI jangan broadcast untuk Livewire requests
            if (isset($wasOnline) && !$wasOnline && $shouldBeOnline && !$this->isLivewireRequest($request)) {
                try {
                    UserOnlineStatus::dispatch($user->id, true);
                } catch (\Exception $e) {
                    Log::error('Failed to broadcast online status: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            // Log error tapi lanjutkan request
            Log::error('Error updating user activity: ' . $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Check if request is from Livewire
     */
    private function isLivewireRequest(Request $request): bool
    {
        // Cek jika request adalah Livewire request
        if ($request->headers->has('X-Livewire')) {
            return true;
        }

        // Cek jika URL mengandung /livewire/
        if (str_contains($request->path(), 'livewire')) {
            return true;
        }

        // Cek jika ada parameter Livewire
        if ($request->has('_livewire')) {
            return true;
        }

        // Cek header khusus Livewire
        if ($request->hasHeader('X-Livewire')) {
            return true;
        }

        return false;
    }

    /**
     * Handle tasks after the response is sent to the browser.
     */
    public function terminate($request, $response): void
    {
        // Optional cleanup logic
    }
}
