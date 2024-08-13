<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Constants\TipoUsuario;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user = null)
    {
        if ($user){
            $data = [
              'message'=>MessageHttp::OBTENIDO_CORRECTAMENTE,
              'data'=>$user
            ];
        }else {
            $usuarios = $this->userService->listarActivos();
            $data = [
                'message'=>MessageHttp::OBTENIDOS_CORRECTAMENTE,
                'data'=>$usuarios
            ];
        }

        return response()->json($data);
    }

    public function obtenerUnPMaestro(){
        $usuario = User::where('tipo', TipoUsuario::PROCURADOR_MAESTRO)
                       ->where('estado', Estado::ACTIVO)
                       ->where('es_eliminado', 0)
                       ->first();
        if ($usuario){

            return $usuario;
        } else {
            return 'No se encontró ningún usuario PROCURADOR_MAESTRO.';
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function listarAbogados()
    {
        $usuarios = $this->userService->listarAbogados();
        $data=[
            'message'=> MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data'=>$usuarios
        ];
        return response()->json($data);

    }
    public function listarProcuradores()
    {
        $usuarios = $this->userService->listarProcuradores();
        $data=[
            'message'=> MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data'=>$usuarios
        ];
        return response()->json($data);

    }
}
