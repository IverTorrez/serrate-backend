<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfilePhotoRequest;
use App\Services\PerfilUsuarioService;
use Illuminate\Support\Facades\Auth;

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
            $perfil = $this->perfilUsuarioService->obtenerPerfil();
            return response()->json([
                'success' => true,
                'data' => $perfil,
                'message' => 'Perfil obtenido con éxito',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener el perfil',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ], 500);
        }
    }

    public function actualizarPerfil(UpdateUserProfileRequest $request): JsonResponse
    {
        try {
            $this->perfilUsuarioService->actualizarPerfil($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado con éxito',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar el perfil',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ], 500);
        }
    }

    public function cambiarPassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->perfilUsuarioService->cambiarPassword($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada con éxito',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar la contraseña',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ], 500);
        }
    }

    public function actualizarFotoPerfil(UpdateProfilePhotoRequest $request): JsonResponse
    {
        try {
            $fotoUrl = $this->perfilUsuarioService->actualizarFotoPerfil($request->file('foto'));
            return response()->json([
                'success' => true,
                'data' => ['foto_url' => $fotoUrl],
                'message' => 'Foto de perfil actualizada con éxito',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar la foto de perfil',
                'message' => $e->getMessage(),
                'statusCode' => 500
            ], 500);
        }
    }
}
