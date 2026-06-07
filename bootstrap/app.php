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

        // SSO-only: guest diarahkan ke halaman login (statis, tanpa efek samping).
        // PENTING: jangan arahkan langsung ke route('ssb.redirect') — route itu
        // men-generate PKCE+state & menulis session, sehingga setiap request guest
        // (halaman, prefetch, favicon, aset terguard) akan memicunya berkali-kali
        // → state saling menimpa → "State tidak valid" di callback. Inisiasi SSO
        // harus SEKALI lewat klik tombol "Masuk dengan SSO" di halaman login.
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (): void {
        //
    })
    ->create();
