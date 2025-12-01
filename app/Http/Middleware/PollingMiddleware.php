<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PollingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add headers to prevent caching for polling requests
        if (str_contains($request->path(), 'livewire/update')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
