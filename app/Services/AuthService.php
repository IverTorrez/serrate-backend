<?php

namespace App\Services;

use App\Constants\ErrorMessages;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Constants\GeneralMessages;
use App\Constants\ValidationMessages;
use App\Http\Resources\Auth\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function login(array $credentials): JsonResponse
    {
        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user) {
                Log::warning('Usuario no encontrado', ['email' => $credentials['email']]);
                return ResponseService::validationError(
                    ['email' => [ValidationMessages::ERROR_VALIDACION_EMAIL]],
                    ErrorMessages::ERROR_AUTENTICACION
                );
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                Log::warning('Contraseña incorrecta', ['email' => $credentials['email']]);
                return ResponseService::validationError(
                    ['password' => [ValidationMessages::ERROR_VALIDACION_PASSWORD]],
                    ErrorMessages::ERROR_AUTENTICACION
                );
            }

            $user->load(['persona']);

            return ResponseService::success([
                'user' => new UserResource($user),
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => now('America/La_Paz')->addMinutes(60)->format('Y-m-d H:i:s'),
            ], GeneralMessages::INICIO_SESION_EXITOSO);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_LOGIN, 500);
        }
    }


    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ResponseService::unauthorized('Usuario no autenticado.');
            }
            $user->currentAccessToken()->delete();

            return ResponseService::success(message: GeneralMessages::CIERRE_SESION_EXITOSO);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al cerrar sesión.', 500);
        }
    }
}
