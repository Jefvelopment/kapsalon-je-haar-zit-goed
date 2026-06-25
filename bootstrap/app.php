<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Belangrijk: TrackPageViews moet in de 'web' groep draaien, NIET als
        // globale append(). De web-groep start de sessie en laadt de
        // ingelogde gebruiker; globale middleware draait daarvoor, waardoor
        // Auth::id() daar altijd null teruggeeft.
        $middleware->web(append: [
            \App\Http\Middleware\TrackPageViews::class,
        ]);

        $middleware->alias([
            'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();