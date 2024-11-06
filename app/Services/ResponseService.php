<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Respuesta para casos exitosos
     */
    public static function success($data = [], $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $status);
    }

    /**
     * Respuesta para errores generales
     */
    public static function error($message = 'Ocurrió un error.', $status = 500): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * Respuesta para errores de validación
     */
    public static function validationError($errors, $message = 'Datos no válidos.', $status = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Respuesta para errores de autenticación
     */
    public static function unauthorized($message = 'No autorizado.', $status = 401): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * Respuesta para recursos no encontrados
     */
    public static function notFound($message = 'Recurso no encontrado.', $status = 404): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * Respuesta para errores de permisos o acceso denegado
     */
    public static function forbidden($message = 'Acceso denegado.', $status = 403): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * Respuesta para cuando una operación es aceptada pero se está procesando
     */
    public static function accepted($data = [], $message = 'Solicitud aceptada, en proceso.', $status = 202): JsonResponse
    {
        return response()->json([
            'status' => 'accepted',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Respuesta para conflictos, como datos duplicados o errores de lógica
     */
    public static function conflict($message = 'Conflicto en la solicitud.', $status = 409): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    /**
     * Respuesta para errores de precondiciones fallidas o solicitudes incompletas
     */
    public static function preconditionFailed($message = 'Precondición fallida.', $status = 412): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
