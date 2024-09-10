<?php

namespace App\Services;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Constants\Estado;
use App\Constants\TipoUsuario;

class AuthService
{
    public function register(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'tipo' => 'required|string|max:50',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'coordenadas' => 'nullable|string|max:200',
            'observacion' => 'nullable|string|max:300',
            'foto_url' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if (!in_array($data['tipo'], TipoUsuario::getValues())) {
            return ['error' => 'Tipo de usuario no válido', 'status' => 400];
        }

        $abogado_id = 0;
        if ($data['tipo'] === TipoUsuario::ABOGADO_DEPENDIENTE) {
            $abogado_id = Auth::user()->id ?? 0;
        }

        $datosJson = [];
        if ($data['tipo'] === TipoUsuario::PROCURADOR) {
            $datosJson = [
                'NO_MOTO' => true,
                'SI_MANEJA_NO_TIENE' => false,
                'SI_MOTO' => false,
            ];
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo' => $data['tipo'],
            'abogado_id' => $abogado_id,
            'opciones_moto' => json_encode($datosJson),
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

        $user->load('persona');
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
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
