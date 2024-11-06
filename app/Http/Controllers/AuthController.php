<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthRequest;
use App\Http\Requests\StoreLoginRequest;
use App\Services\AuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(StoreAuthRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            if ($result['status'] === 'success') {
                return ResponseService::success($result['data'], $result['status_code']);
            }
            return ResponseService::error($result['message'], $result['status_code']);
        } catch (\Exception $e) {
            return ResponseService::error('Error inesperado al registrar usuario.', 500);
        }
    }

    public function login(StoreLoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            if ($result['status'] === 'success') {
                return ResponseService::success($result['data'], $result['status_code']);
            }
            return ResponseService::unauthorized($result['message'], $result['status_code']);
        } catch (\Exception $e) {
            return ResponseService::error('Error inesperado al iniciar sesión.', 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $result = $this->authService->logout();

            return ResponseService::success($result);
        } catch (\Exception $e) {
            return ResponseService::error('Error inesperado al cerrar sesión.', 500);
        }
    }
}
