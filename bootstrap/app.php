<?php

use App\Http\Middleware\LogHttpRequests;
use App\Services\ResponseService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
//use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar Middleware Global
        $middleware->append(LogHttpRequests::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            Log::error($e->getMessage());
        });

        $exceptions->renderable(function (ValidationException $e) {
            return ResponseService::validationError($e->errors(), 'Error de validación');
        });

        $exceptions->renderable(function (AuthenticationException $e) {
            return ResponseService::unauthorized('No autenticado');
        });

        $exceptions->renderable(function (AuthorizationException $e) {
            return ResponseService::forbidden('No tienes permiso para realizar esta acción');
        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            return ResponseService::notFound('Recurso no encontrado');
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e) {
            return ResponseService::error('Método no permitido', 405);
        });

        $exceptions->renderable(function (Throwable $e) {
            return ResponseService::error('Error interno del servidor', 500);
        });
    })

    ->create();
