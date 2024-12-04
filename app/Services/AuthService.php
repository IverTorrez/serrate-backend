<?php

namespace App\Services;

use App\Constants\ErrorMessages;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Constants\Estado;
use App\Constants\GeneralMessages;
use App\Constants\TipoUsuario;
use App\Constants\ValidationMessages;
use App\Models\Billetera;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthService
{
    public function register(array $data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $abogado_id = (Auth::check() && $data['tipo'] === TipoUsuario::ABOGADO_DEPENDIENTE) ? Auth::user()->id : 0;
            $datosJson = $data['opciones_moto'] ?? null;

            $user = User::create([
                'name' => $data['nombre'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'tipo' => $data['tipo'],
                'abogado_id' => $abogado_id,
                'opciones_moto' => $datosJson ? json_encode($datosJson) : null,
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0,
            ]);

            $persona = Persona::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion'] ?? null,
                'coordenadas' => $data['coordenadas'] ?? null,
                'observacion' => $data['observacion'] ?? null,
                'foto_url' => $data['foto_url'] ?? null,
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0,
                'usuario_id' => $user->id,
            ]);

            if (in_array($data['tipo'], [TipoUsuario::ABOGADO_INDEPENDIENTE, TipoUsuario::ABOGADO_LIDER])) {
                Billetera::create([
                    'monto' => 0,
                    'abogado_id' => $user->id,
                    'estado' => Estado::ACTIVO,
                    'es_eliminado' => 0,
                ]);
            }

            DB::commit();

            $user->load('persona');
            $token = $user->createToken('auth_token')->plainTextToken;
            return ResponseService::success(
                [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],

                GeneralMessages::REGISTRO_EXITOSO,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();

            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
    }

    public function login(array $credentials): JsonResponse
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return ResponseService::validationError(
                ['email' => [ValidationMessages::ERROR_VALIDACION_EMAIL]],
                ErrorMessages::ERROR_AUTENTICACION
            );
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return ResponseService::validationError(
                ['password' => [ValidationMessages::ERROR_VALIDACION_PASSWORD]],
                ErrorMessages::ERROR_AUTENTICACION
            );
        }

        $user->load('persona');
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tipo' => $user->tipo,
            'persona' => $user->persona ? [
                'nombre' => $user->persona->nombre,
                'apellido' => $user->persona->apellido,
                'telefono' => $user->persona->telefono,
                'foto_url' => $user->persona->foto_url,
            ] : null,
        ];


        $token = $user->createToken('auth_token')->plainTextToken;
        $expiresAt = now('America/La_Paz')->addMinutes(2)->format('Y-m-d H:i:s');

        return ResponseService::success(
            [
                'user' => $userData,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt
            ],

            GeneralMessages::INICIO_SESION_EXITOSO,
            200
        );
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
