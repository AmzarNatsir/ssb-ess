<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware): void {
        $middleware->alias([
            'must_change_password' => \App\Http\Middleware\CheckMustChangePassword::class,
            'api.token'            => \App\Http\Middleware\ApiTokenAuth::class,
        ]);
    })
    ->withExceptions(function (): void {
        //
    })
    ->create();
