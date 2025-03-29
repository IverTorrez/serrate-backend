<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        if (config('app.env') !== 'production') {
            $estadoPeticion = 'ÉXITO';
            $estadoRespuesta = 'ÉXITO';

            if ($response->getStatusCode() !== 200) {
                $estadoPeticion = 'ERROR';
                $estadoRespuesta = 'ERROR';
            }

            $logData = [
                'ip' => $request->ip(),
                'método' => $request->method(),
                'url' => $request->fullUrl(),
                'parámetros' => $this->getFilteredParameters($request),
                'estado_petición' => $estadoPeticion,
                'respuesta' => [
                    'código_http' => $response->getStatusCode(),
                    'estado' => $estadoRespuesta,
                ],
                'tiempo_de_ejecución' => round(microtime(true) - $startTime, 3) . ' ms',
            ];

            Log::info('📩 PETICIÓN RECIBIDA: ' . json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return $response;
    }

    /**
     * Filtra y devuelve solo los parámetros relevantes para los logs.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function getFilteredParameters(Request $request)
    {
        $parameters = $request->all();

        // Eliminar parámetros sensibles
        unset($parameters['password']);
        unset($parameters['token']);

        return $parameters;
    }
}
