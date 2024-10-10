<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {

            $result = $this->authService->register($request->all());


            if (isset($result['errors'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Errores de validación',
                    'errors' => $result['errors']
                ], 400);
            }


            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error'],
                ], $result['status']);
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Registro exitoso',
                'data' => $result['data'],
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type']
            ], 201);
        } catch (\Exception $e) {

            Log::error('Error inesperado al registrar usuario: ' . $e->getMessage());


            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado. Por favor, inténtalo de nuevo más tarde.'
            ], 500);
        }
    }



    public function login(Request $request)
    {
        $result = $this->authService->login($request->only('email', 'password'));

        if (isset($result['status'])) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result);
    }


    public function logout()
    {
        $result = $this->authService->logout();
        return response()->json($result);
    }
}
