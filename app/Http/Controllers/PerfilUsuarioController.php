<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Services\PerfilUsuarioService;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Constants\ErrorMessages; // Importa la clase de mensajes de error

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
            return $this->perfilUsuarioService->obtenerPerfil();
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_OBTENER_USUARIO, 500);
        }
    }

    public function actualizarPerfil(UpdateUserProfileRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            return $this->perfilUsuarioService->actualizarPerfil($validatedData);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_ACTUALIZAR, 500);
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
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_CAMBIAR_CONTRASENA, 500);
        }
    }

    public function actualizarFotoPerfil(Request $request): JsonResponse
    {
        try {
            $foto = $request->file('foto');
            return $this->perfilUsuarioService->actualizarFotoPerfil($foto);
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_CARGAR_IMAGEN, 500);
        }
    }
}
