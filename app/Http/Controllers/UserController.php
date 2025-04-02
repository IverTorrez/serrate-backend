<?php

namespace App\Http\Controllers;

use App\Constants\ErrorMessages;
use App\Models\User;
use App\Constants\TipoUsuario;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Services\ResponseService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function obtenerUsuariosDependientes(Request $request, $abogadoId)
    {
        $usuarios = $this->userService->obtenerUsuariosDependientes(
            $request,
            $abogadoId

        );
        return UsuarioResource::collection($usuarios);
    }



    public function crearUsuario(RegisterRequest $request): JsonResponse
    {
        try {
            return $this->userService->crearUsuario($request->validated());
        } catch (Exception $e) {
            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
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
        if (Auth::user()->tipo === TipoUsuario::ABOGADO_LIDER) {
            //Lista abogados dependientes del usuario logueado
            $usuarios = $this->userService->abogadosDependientes();
        }
        if (Auth::user()->tipo === TipoUsuario::ADMINISTRADOR) {
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
