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
        $appHost = parse_url((string) env('APP_URL', 'http://pokemon-teams.localhost'), PHP_URL_HOST);

        $middleware->trustHosts(at: array_values(array_unique(array_filter([
            'pokemon-teams.localhost',
            'pokemon-teams.api',
            is_string($appHost) ? $appHost : null,
            'localhost',
            '127.0.0.1',
        ]))));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
