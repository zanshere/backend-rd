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
            // Laravel default web middleware
        ]);

        // API middleware
        $middleware->api([
            // Laravel default API middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handling
    })->create();
