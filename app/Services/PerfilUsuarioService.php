<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PerfilUsuarioService
{
    public function obtenerPerfil(): array
    {
        $user = User::with('persona')->find(Auth::id());

        if (!$user) {
            return [
                'status' => 'error',
                'message' => 'Usuario no autenticado.',
                'status_code' => 401
            ];
        }

        return [
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'tipo' => $user->tipo,
                    'persona' => $user->persona ? [
                        'nombre' => $user->persona->nombre,
                        'apellido' => $user->persona->apellido,
                        'telefono' => $user->persona->telefono,
                        'direccion' => $user->persona->direccion,
                        'observacion' => $user->persona->observacion,
                        'foto_url' => $user->persona->foto_url,
                    ] : null,
                ]
            ],
            'status_code' => 200
        ];
    }

    public function actualizarPerfil(array $data): array
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return [
                'status' => 'error',
                'message' => 'Usuario no autenticado.',
                'status_code' => 401
            ];
        }

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'tipo' => $data['tipo'] ?? $user->tipo
            ]);

            if (isset($data['persona']) && $user->persona) {
                $user->persona->update($data['persona']);
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Perfil actualizado correctamente.',
                'data' => $user->load('persona'),
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'message' => 'Error al actualizar el perfil. ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function cambiarPassword(array $data): array
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return [
                'status' => 'error',
                'message' => 'Usuario no autenticado.',
                'status_code' => 404
            ];
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return [
            'status' => 'success',
            'message' => 'Contraseña cambiada correctamente.'
        ];
    }


    public function actualizarFotoPerfil($foto): array
    {
        $user = Auth::user();

        if (!$user || !$user->persona) {
            return [
                'status' => 'error',
                'message' => 'Usuario no autenticado o sin perfil asociado.',
                'status_code' => 401
            ];
        }

        if (!$foto || !$foto->isValid()) {
            return [
                'status' => 'error',
                'message' => 'Foto no válida.',
                'status_code' => 400
            ];
        }

        if ($user->persona->foto_url) {
            Storage::disk('public')->delete($user->persona->foto_url);
        }

        $nombreUsuario = $user->persona->nombre;
        $extension = $foto->getClientOriginalExtension();
        $nombreArchivo = Str::slug($nombreUsuario) . '_' . $user->persona->apellido . '.' . $extension;

        $rutaFoto = $foto->storeAs('fotos_perfil', $nombreArchivo, 'public');

        if (!$rutaFoto) {
            return [
                'status' => 'error',
                'message' => 'Error al guardar la foto.',
                'status_code' => 500
            ];
        }

        $user->persona->update(['foto_url' => $rutaFoto]);

        return [
            'status' => 'success',
            'message' => 'Foto de perfil actualizada correctamente.',
            'data' => ['foto_url' => Storage::url($rutaFoto)],
            'status_code' => 200
        ];
    }
}
