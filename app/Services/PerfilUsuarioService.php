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

    public function actualizarPerfil(array $data): array
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            return ResponseService::unauthorized('Usuario no autenticado.');
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

    public function actualizarFotoPerfil($foto): array
    {
        $user = Auth::user();

        if (!$user || !$user->persona) {
            return ResponseService::unauthorized('Usuario no autenticado o sin perfil asociado.');
        }

        if (!$foto || !$foto->isValid()) {
            return ResponseService::error('Foto no válida.', 400);
        }

        if ($user->persona->foto_url) {
            Storage::disk('public')->delete($user->persona->foto_url);
        }

        $nombreUsuario = $user->persona->nombre;
        $extension = $foto->getClientOriginalExtension();
        $nombreArchivo = Str::slug($nombreUsuario) . '_' . $user->persona->apellido . '.' . $extension;

        $rutaFoto = $foto->storeAs('fotos_perfil', $nombreArchivo, 'public');

        if (!$rutaFoto) {
            return ResponseService::error('Error al guardar la foto.', 500);
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
