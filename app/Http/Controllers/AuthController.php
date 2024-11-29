<?php

namespace App\Http\Controllers;

use App\Constants\ErrorMessages;
use App\Http\Requests\StoreAuthRequest;
use App\Http\Requests\StoreLoginRequest;
use App\Services\AuthService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(StoreAuthRequest $request): JsonResponse
    {
        try {
            return $this->authService->register($request->validated());
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
    }

    public function login(StoreLoginRequest $request): JsonResponse
    {
        try {
            return $this->authService->login($request->validated());
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_LOGIN, 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $result = $this->authService->logout();
            return ResponseService::success($result);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al cerrar sesión.', 500);
        }
    }
}
