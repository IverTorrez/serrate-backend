<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

// Servicio para estructurar las respuestas JSON
function jsonResponse($status, $message, $code, $data = [])
{
    return response()->json([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ], $code);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Aquí puedes registrar middlewares globales si es necesario
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 1. Autenticación fallida (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return jsonResponse('error', 'No autenticado. Por favor, inicia sesión.', 401);
            }
        });

        // 2. Recurso no encontrado (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return jsonResponse('error', 'Recurso no encontrado.', 404);
            }
        });

        // 3. Método HTTP no permitido (405)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return jsonResponse('error', 'Método no permitido.', 405);
            }
        });

        // 4. Error de validación (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return jsonResponse(
                    'error',
                    'Los datos proporcionados no son válidos.',
                    422,
                    $e->errors()
                );
            }
        });

        // 5. Error de autorización (403)
        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return jsonResponse('error', 'No tienes permiso para acceder a este recurso.', 403);
            }
        });

        // 6. Excepción general para errores internos (500)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                Log::error('Error en API:', [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                ]);

                return jsonResponse('error', 'Ocurrió un error interno en el servidor.', 500);
            }
        });
    })
    ->create();
