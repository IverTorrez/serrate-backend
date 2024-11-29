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

        $token = $user->createToken('auth_token')->plainTextToken;

        return ResponseService::success(
            [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],

            GeneralMessages::INICIO_SESION_EXITOSO,
            200
        );
    }

    public function logout(): array
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens->each(function ($token) {
                $token->delete();
            });
            return [
                'status' => 'success',
                'message' => 'Cierre de sesión exitoso'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Usuario no autenticado',
            'status_code' => 401
        ];
    }
}
