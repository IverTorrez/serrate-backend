<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfilePhotoRequest;
use App\Services\PerfilUsuarioService;
use App\Services\ResponseService;
use Exception;
use Illuminate\Support\Facades\Auth;

class PerfilUsuarioController extends Controller
{
    private PerfilUsuarioService $perfilUsuarioService;

    public function __construct(PerfilUsuarioService $perfilUsuarioService)
    {
        $this->perfilUsuarioService = $perfilUsuarioService;
    }

    public function obtenerPerfil()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return ResponseService::unauthenticated();
            }

            $data = $this->perfilUsuarioService->obtenerPerfil($user);
            return ResponseService::success($data, 'Perfil obtenido correctamente');
        } catch (Exception $e) {
            return ResponseService::error('Error al obtener el perfil', 500, [$e->getMessage()]);
        }
    }

    public function actualizarPerfil(UpdateUserProfileRequest $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return ResponseService::unauthenticated();
            }

            $persona = $this->perfilUsuarioService->actualizarPerfil($user, $request->validated());
            return ResponseService::success($persona, 'Perfil actualizado correctamente');
        } catch (Exception $e) {
            return ResponseService::error('Error al actualizar el perfil', 500, [$e->getMessage()]);
        }
    }

    public function cambiarPassword(UpdatePasswordRequest $request)
    {
        try {
            $this->perfilUsuarioService->cambiarPassword($request->validated());
            return ResponseService::success([], 'Contraseña cambiada correctamente');
        } catch (\Exception $e) {
            return ResponseService::error('Error al cambiar la contraseña', 500, [$e->getMessage()]);
        }
    }



    public function actualizarFotoPerfil(UpdateProfilePhotoRequest $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return ResponseService::unauthenticated();
            }

            $fotoUrl = $this->perfilUsuarioService->actualizarFotoPerfil($user, $request->file('foto'));
            return ResponseService::success(['foto_url' => $fotoUrl], 'Foto de perfil actualizada');
        } catch (Exception $e) {
            return ResponseService::error('Error al actualizar la foto de perfil', 500, [$e->getMessage()]);
        }
    }
}
