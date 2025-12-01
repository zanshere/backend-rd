<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            'role' => App\Http\Middleware\CheckRole::class,
            'admin' => App\Http\Middleware\AdminOnly::class,
            'order.owner' => App\Http\Middleware\CheckOrderOwnership::class,
            'user.role' => App\Http\Middleware\CheckUserRole::class,
            'user.active' => App\Http\Middleware\UserActive::class,
        ]);

        // Global middleware that runs on every request
        $middleware->web([
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UpdateUserActivity::class,
            \App\Http\Middleware\PollingMiddleware::class,
        ]);

        // API middleware
        $middleware->api([
            // Laravel default API middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handling
        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof \Livewire\Exceptions\MethodNotFoundException) {
                return response()->json([
                    'error' => 'Method not found',
                    'message' => $e->getMessage(),
                    'trace' => config('app.debug') ? $e->getTrace() : null,
                ], 404);
            }

            // Jangan panggil parent di sini, biarkan Laravel menangani exception lainnya
            return null;
        });
    })
    ->withProviders([
        // Register service providers
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\ScheduleServiceProvider::class,
        App\Providers\AppServiceProvider::class,
    ])
    ->create();
