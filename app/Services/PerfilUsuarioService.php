<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
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
            return ResponseService::unauthorized('Usuario no autenticado.');
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
            'status_code' => 200,
            'message' => 'Perfil obtenido correctamente.'
        ];
    }

    public function actualizarPerfil(array $data): JsonResponse
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::error('Usuario no autenticado', 401);
        }

        DB::beginTransaction();
        try {
            // Actualizar los datos del usuario
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'tipo' => $data['tipo'] ?? $user->tipo
            ]);


            if (isset($data['persona']) && $user->persona) {
                $user->persona->update($data['persona']);
            }

            DB::commit();

            return ResponseService::success(
                $user->load('persona'),
                'Perfil actualizado correctamente.',
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseService::error('Error al actualizar el perfil. ' . $e->getMessage(), 500);
        }
    }



    public function cambiarPassword(array $data): array
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::unauthorized('Usuario no autenticado.');
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return [
            'status' => 'success',
            'message' => 'Contraseña cambiada correctamente.',
            'status_code' => 200
        ];
    }

    public function actualizarFotoPerfil($foto): JsonResponse
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::error('Usuario no autenticado', 401);
        }

        if (!$foto || !$foto->isValid()) {
            return ResponseService::error('Foto no válida.', 400);
        }

        if ($user->persona->foto_url) {
            Storage::disk('public')->delete($user->persona->foto_url);
        }

        $extension = $foto->getClientOriginalExtension();
        $nombreArchivo = Str::slug($user->persona->nombre . '_' . $user->persona->apellido) . '_' . time() . '.' . $extension;


        $rutaFoto = $foto->storeAs('fotos_perfil', $nombreArchivo, 'public');

        if (!$rutaFoto) {
            return ResponseService::error('Error al guardar la foto.', 500);
        }

        $user->persona->update(['foto_url' => $rutaFoto]);

        return ResponseService::success(
            ['foto_url' => $rutaFoto],
            'Foto de perfil actualizada correctamente.',
            200
        );
    }
}
