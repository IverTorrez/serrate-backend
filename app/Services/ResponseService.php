<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public static function success($data = [], $message = 'Operación exitosa', $status = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }


    public static function error($message = 'Ocurrió un error.', $status = 500, $errors = null): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public static function validationError($errors, $message = 'Datos no válidos.', $status = 422): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public static function unauthorized($message = 'No autorizado.', $status = 401): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    public static function notFound($message = 'Recurso no encontrado.', $status = 404): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }

    public static function forbidden($message = 'Acceso denegado.', $status = 403): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
