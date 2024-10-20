<?php

namespace App\Services;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Constants\Estado;
use App\Constants\TipoUsuario;
use App\Models\Billetera;
use Illuminate\Support\Facades\DB;
use Laravel\Reverb\Loggers\Log;

class AuthService
{
    public function register(array $data)
    {
        try {

            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'tipo' => 'required|string|in:' . implode(',', TipoUsuario::getValues()),
                'apellido' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:255',
                'coordenadas' => 'nullable|string|max:200',
                'observacion' => 'nullable|string|max:300',
                'foto_url' => 'nullable|string|max:200',
                'opciones_moto' => 'nullable|array',
            ]);


            if ($validator->fails()) {
                return [
                    'success' => false,
                    'errors' => $validator->errors(),
                    'status' => 400
                ];
            }


            $abogado_id = (Auth::check() && $data['tipo'] === TipoUsuario::ABOGADO_DEPENDIENTE) ? Auth::user()->id : 0;
            $datosJson = $data['opciones_moto'] ?? null;


            DB::beginTransaction();


            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'tipo' => $data['tipo'],
                'abogado_id' => $abogado_id,
                'opciones_moto' => $datosJson ? json_encode($datosJson) : null,
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0,
            ]);


            $persona = Persona::create([
                'nombre' => $data['name'],
                'apellido' => $data['apellido'],
                'telefono' => $data['telefono'],
                'direccion' => $data['direccion'],
                'coordenadas' => $data['coordenadas'],
                'observacion' => $data['observacion'],
                'foto_url' => $data['foto_url'],
                'estado' => Estado::ACTIVO,
                'es_eliminado' => 0,
                'usuario_id' => $user->id,
            ]);

            if ($data['tipo'] === TipoUsuario::ABOGADO_INDEPENDIENTE || $data['tipo'] === TipoUsuario::ABOGADO_LIDER) {
                $billetera = Billetera::create([
                    'monto' => 0,
                    'abogado_id' => $user->id,
                    'estado' => Estado::ACTIVO,
                    'es_eliminado' => 0,
                ]);
            }


            DB::commit();


            $user->load('persona');


            $token = $user->createToken('auth_token')->plainTextToken;


            return [
                'success' => true,
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
        } catch (\Exception $e) {

            Log::error('Error al registrar usuario: ' . $e->getMessage());


            return [
                'success' => false,
                'error' => 'Error al procesar el registro, por favor intente más tarde',
                'status' => 500
            ];
        }
    }




    public function login(array $credentials)
    {

        if (!Auth::attempt($credentials)) {
            return ['message' => 'No autorizado', 'status' => 401];
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Hi ' . $user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }

    public function logout()
    {

        auth()->user()->tokens()->delete();

        return ['message' => 'Cierre de sesión exitoso'];
    }
}
