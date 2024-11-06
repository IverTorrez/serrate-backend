<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de errores de autenticación (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No autenticado. Por favor, inicia sesión.'
                ], 401);
            }
        });

        // Manejo para rutas no encontradas (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Recurso no encontrado.'
                ], 404);
            }
        });

        // Manejo para métodos no permitidos (405)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Método no permitido.'
                ], 405);
            }
        });

        // Manejo de errores de validación (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return \App\Services\ResponseService::validationError(
                    $e->errors(),
                    'Los datos proporcionados no son válidos.'
                );
            }
        });

        // Manejo de errores de autorización (403)
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tienes permiso para acceder a este recurso.'
                ], 403);
            }
        });

        // Manejo general de excepciones
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Registro de la excepción para facilitar la depuración
                Log::error('Error en API:', [
                    'exception' => $e,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Ocurrió un error interno en el servidor.',
                ], 500);
            }
        });
    })
    ->create();
