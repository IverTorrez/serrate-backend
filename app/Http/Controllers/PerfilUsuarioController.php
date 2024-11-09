<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Services\PerfilUsuarioService;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PerfilUsuarioController extends Controller
{
    protected $perfilUsuarioService;

    public function __construct(PerfilUsuarioService $perfilUsuarioService)
    {
        $this->perfilUsuarioService = $perfilUsuarioService;
    }

    public function obtenerPerfil(): JsonResponse
    {
        try {
            $response = $this->perfilUsuarioService->obtenerPerfil();
            return $response['status'] === 'success'
                ? ResponseService::success($response['data'], $response['message'], $response['status_code'])
                : ResponseService::error($response['message'], $response['status_code']);
        } catch (\Exception $e) {
            return ResponseService::error('Error inesperado al obtener el perfil.', 500);
        }
    }

    public function actualizarPerfil(UpdateUserProfileRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            return $this->perfilUsuarioService->actualizarPerfil($validatedData);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al actualizar el perfil.', 500);
        }
    }



    public function cambiarPassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->perfilUsuarioService->cambiarPassword($validatedData);
            return $response['status'] === 'success'
                ? ResponseService::success([], $response['message'])
                : ResponseService::error($response['message'], $response['status_code']);
        } catch (\Exception $e) {
            return ResponseService::error('Error inesperado al cambiar la contraseña.', 500);
        }
    }

    public function actualizarFotoPerfil(Request $request): JsonResponse
    {
        try {

            $foto = $request->file('foto');
            return $this->perfilUsuarioService->actualizarFotoPerfil($foto);
        } catch (Exception $e) {
            return ResponseService::error('Error inesperado al actualizar la foto de perfil.', 500);
        }
    }
}
