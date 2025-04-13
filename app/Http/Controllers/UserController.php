<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\TipoUsuario;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Http\Requests\Usuarios\StoreUserRegisterRequest;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function crearUsuario(StoreUserRegisterRequest $request): JsonResponse
    {
        return $this->userService->crearUsuario($request->validated());
    }

    public function obtenerUsuariosDependientes(Request $request, $abogadoId)
    {
        $usuarios = $this->userService->obtenerUsuariosDependientes(
            $request,
            $abogadoId

        );
        return UsuarioResource::collection($usuarios);
    }

    public function show(User $user = null)
    {
        if ($user) {
            $data = [
                'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
                'data' => $user
            ];
        } else {
            $usuarios = $this->userService->listarActivos();
            $data = [
                'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data' => $usuarios
            ];
        }

        return response()->json($data);
    }

    public function obtenerUnPMaestro()
    {
        $usuario = User::where('tipo', TipoUsuario::PROCURADOR_MAESTRO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        if ($usuario) {

            return $usuario;
        } else {
            return 'No se encontró ningún usuario PROCURADOR_MAESTRO.';
        }
    }

    public function listarAbogados()
    {
        $tipoUsuario = Auth::user()->tipo;
        $usuarios = '';
        if ($tipoUsuario === TipoUsuario::ABOGADO_LIDER) {
            //Lista abogados dependientes del usuario logueado
            $usuarios = $this->userService->abogadosDependientes();
        }
        if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::CONTADOR || $tipoUsuario === TipoUsuario::PROCURADOR_MAESTRO) {
            //Todos los abogados
            $usuarios = $this->userService->listarAbogados();
        }
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
    public function listarAbogadosDependientes($abogadoLiderId)
    {
        $usuarios = $this->userService->listarAbogadosDependientes($abogadoLiderId);
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
    public function listarProcuradores()
    {
        $usuarios = $this->userService->listarProcuradores();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $usuarios
        ];
        return response()->json($data);
    }
}
