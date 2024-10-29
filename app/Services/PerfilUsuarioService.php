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
            throw new \Exception("Usuario no autenticado.");
        }
        return [
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
        ];
    }

    public function actualizarPerfil(array $data): bool
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \Exception("Usuario no autenticado.");
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
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Error al actualizar el perfil: ' . $e->getMessage());
        }
    }

    public function cambiarPassword(array $data): bool
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \Exception("Usuario no autenticado.");
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            throw new \Exception("La contraseña actual es incorrecta.");
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return true;
    }

    public function actualizarFotoPerfil($foto): string
    {
        $user = Auth::user();

        if (!$user || !$user->persona) {
            throw new \Exception("Usuario no autenticado o sin perfil asociado.");
        }

        if (!$foto || !$foto->isValid()) {
            throw new \Exception("Foto no válida.");
        }

        if ($user->persona->foto_url) {
            Storage::disk('public')->delete($user->persona->foto_url);
        }


        $nombreUsuario = $user->persona->nombre;
        $extension = $foto->getClientOriginalExtension();
        $nombreArchivo = Str::slug($nombreUsuario) . '_' . $user->persona->apellido . '.' . $extension;

        $rutaFoto = $foto->storeAs('fotos_perfil', $nombreArchivo, 'public');
        if (!$rutaFoto) {
            throw new \Exception("Error al guardar la foto.");
        }

        $user->persona->update(['foto_url' => $rutaFoto]);

        return Storage::url($rutaFoto);
    }
}
