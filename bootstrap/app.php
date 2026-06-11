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
        // Alias middleware
        $middleware->alias([
            'tenant'             => \App\Http\Middleware\EnsureTenantAccess::class,
            'set_tenant'         => \App\Http\Middleware\SetActiveTenant::class,
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Jalankan SetActiveTenant di semua request web yang sudah login
        $middleware->appendToGroup('web', \App\Http\Middleware\SetActiveTenant::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
