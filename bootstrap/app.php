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
        $middleware->append(\App\Http\Middleware\EnforceValidRole::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'cashier' => \App\Http\Middleware\CashierMiddleware::class,
            'concessionaire' => \App\Http\Middleware\ConcessionaireMiddleware::class,
            'faculty' => \App\Http\Middleware\FacultyMiddleware::class,
            'local.env.only' => \App\Http\Middleware\LocalEnvOnlyMiddleware::class,
            'partnership.access' => \App\Http\Middleware\PartnershipAccessMiddleware::class,
            'restrict.admin.panel' => \App\Http\Middleware\RestrictAdminToAdminPanel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
